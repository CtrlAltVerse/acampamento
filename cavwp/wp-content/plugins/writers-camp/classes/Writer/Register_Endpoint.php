<?php

namespace writersCampP\Writer;

use cavWP\Networks\Utils as NetworksUtils;
use WP_Error;
use WP_REST_Response;
use WP_REST_Server;

class Register_Endpoint
{
   public function __construct()
   {
      add_action('rest_api_init', [$this, '_create_endpoints']);
   }

   public function _create_endpoints(): void
   {
      register_rest_route('wrs-camp/v1', '/avatar', [
         'methods'             => WP_REST_Server::EDITABLE,
         'callback'            => [$this, 'avatar'],
         'permission_callback' => ['writersCampP\Utils', 'checks_login'],
         'parse_errors'        => true,
      ]);

      register_rest_route('wrs-camp/v1', '/new_pass', [
         'methods'             => WP_REST_Server::EDITABLE,
         'callback'            => [$this, 'new_pass'],
         'permission_callback' => '__return_true',
         'parse_errors'        => true,
         'args'                => Utils::get_retrieve_fields(),
      ]);

      register_rest_route('wrs-camp/v1', '/retrieve', [
         'methods'             => WP_REST_Server::EDITABLE,
         'callback'            => [$this, 'retrieve'],
         'permission_callback' => '__return_true',
         'parse_errors'        => true,
         'args'                => [
            'user_email' => [
               'type'      => 'string',
               'format'    => 'email',
               'minLength' => 6,
               'maxLength' => 44,
               'required'  => true,
            ],
         ],
      ]);

      register_rest_route('wrs-camp/v1', '/check', [
         'methods'             => WP_REST_Server::EDITABLE,
         'callback'            => [$this, 'check'],
         'permission_callback' => '__return_true',
         'parse_errors'        => true,
         'args'                => [
            'sign_method' => [
               'type'     => 'string',
               'enum'     => ['google', 'facebook'],
               'required' => true,
            ],
            'token' => [
               'type'     => 'string',
               'required' => true,
            ],
         ],
      ]);

      register_rest_route('wrs-camp/v1', '/enter', [
         'methods'             => WP_REST_Server::EDITABLE,
         'callback'            => [$this, 'enter'],
         'permission_callback' => '__return_true',
         'parse_errors'        => true,
         'args'                => Utils::get_sign_fields(),
      ]);

      register_rest_route('wrs-camp/v1', '/profile', [
         'methods'             => WP_REST_Server::EDITABLE,
         'callback'            => [$this, 'update_profile'],
         'permission_callback' => ['writersCampP\Utils', 'checks_login'],
         'parse_errors'        => true,
         'args'                => Utils::get_profile_fields(),
      ]);
   }

   public function avatar($request)
   {
      $files = $request->get_file_params();

      if (empty($files['avatar'])) {
         return new WP_Error('cav_rest_missing_file', 'Arquivo não recebido.');
      }

      $ext              = pathinfo($files['avatar']['full_path'], PATHINFO_EXTENSION);
      $upload_file      = wp_upload_bits($files['avatar']['full_path'], null, @file_get_contents($files['avatar']['tmp_name']));
      $destination_file = wp_generate_uuid4() . '.' . $ext;
      $destination_full = wp_upload_dir()['basedir'] . '/avatares/' . $destination_file;
      $current_image    = wp_get_image_editor($upload_file['file']);

      if (is_wp_error($current_image)) {
         return $current_image;
      }

      $current_image->resize(96, 96, true);
      $current_image->set_quality(80);
      $current_image->save($destination_full);

      unlink($upload_file['file']);

      update_user_meta(get_current_user_id(), 'avatar_uploaded', $destination_file);
      update_user_meta(get_current_user_id(), 'avatar_source', 'upload');

      $actions[] = [
         'action'  => 'toast',
         'content' => 'Avatar atualizado com sucesso.',
      ];

      return new WP_REST_Response($actions);
   }

   public function check($request)
   {
      $body = $request->get_params();

      $actions = [];

      if ('facebook' === $body['sign_method']) {
         $request = wp_remote_get('https://graph.facebook.com/v23.0/me?fields=id,name,email,picture&access_token=' . $body['token'], [
            'cache' => false,
         ]);

         if (\is_wp_error($request) || wp_remote_retrieve_response_code($request) !== 200) {
            return new WP_REST_Response([
               'action'  => 'toast',
               'content' => 'Tente novamente mais tarde.',
            ]);
         }

         $profile = json_decode(\wp_remote_retrieve_body($request), true);

         $email_verified         = true;
         $user['social_user_id'] = $profile['id'];
         $user['display_name']   = $profile['name'];
         $user['user_email']     = $profile['email'];
         $user['avatar_url']     = $profile['picture']['data']['url'];
      }

      if ('google' === $body['sign_method']) {
         $user_google = NetworksUtils::decode_google_jwt($body['token']);

         $email_verified = (bool) $user_google['email_verified'];

         $user['social_user_id'] = $user_google['sub'];
         $user['display_name']   = $user_google['name'];
         $user['user_email']     = $user_google['email'];
         $user['avatar_url']     = $user_google['picture'];
      }

      $User = get_user_by('email', $user['user_email']);

      if (!empty($User)) {
         if (!empty($user['social_user_id']) && !empty($body['sign_method'])) {
            $network_user_id = get_user_meta($User->ID, "network_user_id_{$body['sign_method']}", true);

            if ($network_user_id === $user['social_user_id']) {
               wp_set_auth_cookie($User->ID, true, is_ssl());

               $actions[] = [
                  'action'  => 'toast',
                  'content' => 'Entrou com sucesso. Aguarde um momento.',
                  'extra'   => 3,
               ];

               $actions[] = [
                  'action' => 'reload',
                  'extra'  => 3,
               ];
            } else {
               $actions[] = [
                  'action'  => 'toast',
                  'content' => 'Email já cadastrado com outro método.',
               ];

               $actions[] = [
                  'action' => 'go',
                  'target' => add_query_arg(['action' => 'login'], home_url()),
                  'extra'  => 3,
               ];
            }
         }
      } else {
         $actions[] = [
            'action'  => 'value',
            'target'  => '#display_name',
            'content' => $user['display_name'],
         ];

         $actions[] = [
            'action'  => 'value',
            'target'  => '#user_email',
            'content' => $user['user_email'],
         ];

         $actions[] = [
            'action'  => 'value',
            'target'  => '#social_user_id',
            'content' => $user['social_user_id'],
         ];

         set_transient("social_login-{$body['sign_method']}-{$user['social_user_id']}", [
            'avatar_url'     => $user['avatar_url'],
            'user_email'     => $user['user_email'],
            'social_user_id' => $user['social_user_id'],
            'email_verified' => $email_verified,
         ], HOUR_IN_SECONDS);
      }

      return new WP_REST_Response($actions);
   }

   public function enter($request)
   {
      if (is_user_logged_in()) {
         $actions[] = ['action' => 'reload'];
      } else {
         $body = $request->get_params();

         $user['user_email']   = $body['user_email']   ?? null;
         $user['user_pass']    = $body['user_pass']    ?? null;
         $user['display_name'] = $body['display_name'] ?? null;
         $user['user_login']   = $body['user_login']   ?? null;

         // CADASTRAR
         if (!empty($body['is_signup'])) {
            if (!empty($body['social_user_id']) && !empty($body['sign_method'])) {
               $social_user = get_transient("social_login-{$body['sign_method']}-{$body['social_user_id']}");

               if (false === $social_user) {
                  return new WP_REST_Response([
                     'action'  => 'toast',
                     'content' => 'Sessão expirada. Tente novamente.',
                  ]);
               }

               if (false === $social_user['email_verified']) {
                  $body['meta_input']['email_not_verified'] = true;
               }

               $user['avatar_url']     = $social_user['avatar_url'];
               $user['user_email']     = $social_user['user_email'];
               $user['social_user_id'] = $social_user['social_user_id'];

               if (!empty($user['avatar_url'])) {
                  $user['meta_input']['avatar_url']    = $user['avatar_url'];
                  $user['meta_input']['avatar_source'] = 'network';
               }

               $user['meta_input']["network_user_id_{$body['sign_method']}"] = $user['social_user_id'];
            }

            if (!isset($social_user['email_verified'])) {
               $user['meta_input']['email_not_verified'] = true;
            }

            if ($user['meta_input']['email_not_verified']) {
               $user['meta_input']['email_not_verified'] = wp_generate_uuid4();
            }

            if (empty($body['user_pass'])) {
               $user['user_pass'] = wp_generate_password();
            }

            $user['role'] = 'contributor';
            $user_ID      = wp_insert_user($user);

            if (is_wp_error($user_ID)) {
               return $user_ID;
            }

            wp_set_auth_cookie($user_ID, true, is_ssl());

            $actions[] = [
               'action'  => 'toast',
               'content' => 'Cadastro realizado com sucesso. Aguarde um momento.',
               'extra'   => 3,
            ];

            $actions[] = [
               'action' => 'reload',
               'extra'  => 3,
            ];

         // LOGIN
         } else {
            $user = wp_signon([
               'user_login'    => $user['user_email'],
               'user_password' => $user['user_pass'],
               'remember'      => true,
            ]);

            if (is_wp_error($user)) {
               return $user;
            }

            $actions[] = [
               'action'  => 'toast',
               'content' => 'Entrou com sucesso. Aguarde um momento.',
               'extra'   => 3,
            ];

            $actions[] = [
               'action' => 'reload',
               'extra'  => 3,
            ];
         }
      }

      return new WP_REST_Response($actions);
   }

   public function new_pass($request)
   {
      $body = $request->get_params();

      $user = check_password_reset_key($body['rp_key'], $body['rp_login']);

      if (is_wp_error($user)) {
         return $user;
      }

      reset_password($user, $body['rp_pass']);

      $actions[] = [
         'action'  => 'toast',
         'content' => 'Senha alterada com sucesso. Aguarde um momento.',
         'extra'   => 3,
      ];
      $actions[] = [
         'action' => 'go',
         'target' => add_query_arg(['action' => 'login'], home_url()),
         'extra'  => 3,
      ];

      return new WP_REST_Response($actions);
   }

   public function retrieve($request)
   {
      $body = $request->get_params();

      $actions = [];

      ob_start();
      retrieve_password($body['user_email']);
      ob_clean();

      $actions[] = [
         'action'  => 'toast',
         'content' => 'Se há uma conta com este e-mail, siga os passos enviados a ele.',
      ];

      return new WP_REST_Response($actions);
   }

   public function update_profile($request)
   {
      $body = $request->get_params();

      $actions = [];

      $User = wp_get_current_user();

      $userdata['ID'] = $User->ID;

      $userdata['display_name'] = $body['display_name'];
      $userdata['user_email']   = $body['user_email'];

      if (!empty($body['description'])) {
         $userdata['description'] = $body['description'];
      }

      if (!empty($body['site_url'])) {
         $userdata['user_url'] = $body['site_url'];
      }

      if (!empty($body['avatar'])) {
         $userdata['meta_input']['avatar_uploaded'] = $body['avatar'];
         $userdata['meta_input']['avatar_source']   = 'upload';
      }

      // CHANGING PASS
      if (!empty($body['old_password']) && !empty($body['user_password']) && $body['old_password'] !== $body['user_password']) {
         $check = wp_authenticate($User->user_login, $body['old_password']);

         if (is_wp_error($check)) {
            $actions[] = [
               'action'  => 'toast',
               'content' => 'Senha não alterada. Senha antiga não confere.',
            ];
         } else {
            $userdata['user_pass'] = $body['user_password'];
         }
      }

      // NOT CHANGING PASS
      if ((empty($body['old_password']) && !empty($body['user_password'])) || (!empty($body['old_password']) && empty($body['user_password']))) {
         $actions[] = [
            'action'  => 'toast',
            'content' => 'Senha não alterada. Preencha os dois campos.',
         ];
      }

      $all_socials = array_keys(NetworksUtils::get_services('profile'));

      foreach ($all_socials as $key) {
         if (empty($body[$key])) {
            continue;
         }

         $userdata[$key] = $body[$key];
      }

      $user_ID = wp_update_user($userdata);

      if (is_wp_error($user_ID)) {
         return $user_ID;
      }

      $actions[] = [
         'action'  => 'toast',
         'content' => 'Perfil atualizado com sucesso.',
      ];

      return new WP_REST_Response($actions);
   }
}
