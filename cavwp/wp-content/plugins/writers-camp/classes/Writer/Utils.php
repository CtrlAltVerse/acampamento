<?php

namespace writersCampP\Writer;

use cavWP\Networks\Utils as NetworksUtils;
use cavWP\Validate;
use writersCampP\Utils as WritersCampUtils;

class Utils
{
   public static function edit_url($post_ID = null, $challenge = 0, $slot = -1)
   {
      $publishing = WritersCampUtils::get_page_link('edit');

      if (!empty($post_ID)) {
         $args['edit'] = $post_ID;
      }

      if (!empty($challenge)) {
         $args['desafio'] = $challenge;
      }

      if ($slot >= 0) {
         $args['slot'] = $slot;
      }

      return esc_url(add_query_arg($args, $publishing));
   }

   public static function get_avatar_fields()
   {
      return [
         'avatar' => [
            'title'    => 'Avatar',
            'type'     => 'file',
            'format'   => 'image',
            'required' => true,
         ],
      ];
   }

   public static function get_profile_fields()
   {
      $Validade = new Validate();

      $socials = NetworksUtils::get_services('profile');

      $fields = [
         'display_name' => [
            'title'             => 'Nome',
            'type'              => 'string',
            'not'               => ['special', 'number'],
            'minLength'         => 3,
            'maxLength'         => 33,
            'required'          => true,
            'validate_callback' => [$Validade, 'check'],
         ],
         'site_url' => [
            'title'             => 'Site pessoal',
            'type'              => 'string',
            'format'            => 'url',
            'maxLength'         => 99,
            'validate_callback' => [$Validade, 'check'],
         ],
         'user_email' => [
            'title'       => 'E-mail',
            'description' => 'Ao alterar o e-mail, será preciso confirmá-lo',
            'type'        => 'string',
            'format'      => 'email',
            'required'    => true,
         ],
         'old_password' => [
            'title'             => 'Senha antiga',
            'type'              => 'string',
            'format'            => 'password',
            'minLength'         => 6,
            'maxLength'         => 33,
            'validate_callback' => [$Validade, 'check'],
         ],
         'user_password' => [
            'title'             => 'Senha nova',
            'type'              => 'string',
            'format'            => 'password',
            'minLength'         => 6,
            'maxLength'         => 33,
            'has'               => ['lowercase', 'uppercase', 'special', 'number'],
            'validate_callback' => [$Validade, 'check'],
         ],
         'description' => [
            'title'     => 'Apresentação',
            'type'      => 'string',
            'maxlength' => 600,
         ],
      ];

      foreach ($socials as $key => $social) {
         $fields[$key] = [
            'title'             => $social['name'],
            'type'              => 'string',
            'description'       => 'url' === ($social['profile_type'] ?? '') ? 'Insira a URL completa' : 'ID ou Perfil sem @',
            'format'            => $social['profile_type'] ?? null,
            'minLength'         => 2,
            'maxlength'         => 33,
            'has_profile'       => $social['profile'],
            'validate_callback' => [$Validade, 'check'],
         ];
      }

      return $fields;
   }

   public static function get_retrieve_fields()
   {
      return [
         'rp_pass' => [
            'title'     => 'Nova senha',
            'type'      => 'string',
            'format'    => 'password',
            'has'       => ['lowercase', 'uppercase', 'special', 'number'],
            'required'  => true,
            'minLength' => 6,
            'maxLength' => 33,
         ],
         'rp_key' => [
            'type'     => 'string',
            'required' => true,
         ],
         'rp_login' => [
            'type'     => 'string',
            'required' => true,
         ],
      ];
   }

   public static function get_sign_fields()
   {
      $Validade = new Validate();

      return [
         'is_signup' => [
            'title'   => 'Cadastrar',
            'type'    => 'boolean',
            'default' => false,
         ],
         'sign_method' => [
            'type'     => 'string',
            'enum'     => ['google', 'facebook', 'email', 'login'],
            'required' => true,
         ],
         'social_user_id' => [
            'type' => 'string',
         ],
         'display_name' => [
            'title'             => 'Nome',
            'type'              => 'string',
            'not'               => ['special', 'number'],
            'special_allow'     => '\'',
            'minLength'         => 3,
            'maxLength'         => 33,
            'validate_callback' => [$Validade, 'check'],
         ],
         'user_login' => [
            'title'             => 'Usuário',
            'description'       => 'Não poderá ser alterado no futuro',
            'type'              => 'string',
            'not'               => ['special', 'uppercase', 'space'],
            'special_allow'     => '-_',
            'minLength'         => 3,
            'maxLength'         => 22,
            'validate_callback' => [$Validade, 'check'],
         ],
         'user_email' => [
            'title'     => 'E-mail',
            'type'      => 'string',
            'format'    => 'email',
            'minLength' => 6,
            'maxLength' => 44,
            'required'  => true,
         ],
         'user_pass' => [
            'title'             => 'Senha',
            'type'              => 'string',
            'format'            => 'password',
            'has'               => ['lowercase', 'uppercase', 'special', 'number'],
            'minLength'         => 6,
            'maxLength'         => 33,
            'validate_callback' => [$Validade, 'check'],
         ],
      ];
   }
}
