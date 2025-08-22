<?php

namespace writersCampP\Writer;

class Register
{
   public function __construct()
   {
      add_filter('get_avatar_url', [$this, 'get_avatar_url'], 10, 2);
      add_filter('site_url', [$this, 'set_retrieve_password_url'], 10, 3);

      add_action('user_register', [$this, 'send_mail_new'], 10, 2);
      add_action('pre_get_posts', [$this, 'set_author_query']);

      add_action('template_redirect', [$this, 'get_mail_confirmation']);
      add_action('admin_init', [$this, 'block_wp_admin']);

      new Register_Endpoint();
   }

   public function block_wp_admin()
   {
      if (is_admin() && !current_user_can('manage_options') && !wp_doing_ajax()) {
         wp_die('Sem permissão.');
      }
   }

   public function get_avatar_url($avatar_url, $_user)
   {
      if (empty($_user) || is_a($_user, 'WP_Comment') || is_bool($_user)) {
         return $avatar_url;
      }

      if (is_a($_user, 'WP_User')) {
         $user_ID = $_user->ID;
      } elseif (is_numeric($_user)) {
         $user_ID = $_user;
      } else {
         $User = get_user_by('email', $_user);

         if (is_bool($User)) {
            return $avatar_url;
         }

         $user_ID = $User->ID;
      }

      $avatar_source = get_user_meta($user_ID, 'avatar_source', true);

      if ('network' === $avatar_source) {
         $network_avatar_url = get_user_meta($user_ID, 'avatar_url', true);

         if (!empty($network_avatar_url)) {
            return $network_avatar_url;
         }
      }

      if ('upload' === $avatar_source) {
         $uploaded_file = get_user_meta($user_ID, 'avatar_uploaded', true);

         if (!empty($uploaded_file)) {
            return wp_upload_dir()['baseurl'] . '/avatares/' . $uploaded_file;
         }
      }

      return $avatar_url;
   }

   public function get_mail_confirmation()
   {
      if (empty($_GET['action']) || empty($_GET['code']) || 'confirmation' !== $_GET['action']) {
         return;
      }

      $users = get_users([
         'meta_key'   => 'email_not_verified',
         'meta_value' => sanitize_text_field($_GET['code']),
      ]);

      if (empty($users)) {
         return;
      }

      delete_user_meta($users[0]->ID, 'email_not_verified');
   }

   public function send_mail_new($user_ID, $userdata)
   {
      $User     = get_user_by('ID', $user_ID);
      $sitename = get_bloginfo('name');
      $to       = "{$User->display_name} <{$User->user_email}>";
      $img      = get_field('email-logo', 'option');

      if (isset($userdata['meta_input']['email_not_verified'])) {
         $url  = home_url('?action=confirmation&code=' . $userdata['meta_input']['email_not_verified']);
         $link = <<<HTML
            <a href="{$url}" target="_blank" style="color:#eee">Confirmar e-mail</a>
         HTML;
      } else {
         $url  = home_url('?action=login');
         $link = <<<HTML
            <a href="{$url}" target="_blank" style="color:#eee">Entrar</a>
         HTML;
      }

      $message = <<<HTML
      <div style="line-heigth:1.35;font-size:20px;color:#f6f6f4;margin:auto;width:640px;background-color:#2a2225;border-radius:8px;font-family:ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';">
         <div style="padding-top:32px;background-color:#25938f;border-top-left-radius:8px;border-top-right-radius:8px;">
            <img src="{$img}" alt style="display:block;width:640px;height:207px" />
         </div>
         <div style="padding:32px">
            <p>Seja bem-vinda, bem-vindo, bem-vinde viajante.</p>

            <p>O <strong>{$sitename}</strong> é um lugar para todos os níveis de <em>bardos</em> descansarem de suas aventuras, contarem histórias e trocarem experiências.</p>

            <p>Publique seus textos, cumpra nossos desafios, interaja com outros autores, suba de nível!</p>

            <p>Conheça nossas quatro guildas e descubra qual delas você tem mais afinidade.</p>
            <p style="font-weight:bold;">{$link}</p>
         </div>
      </div>
      <div style="margin-top:8px;text-align:center;font-size:14px;font-family:ui-sans-serif, system-ui, sans-serif">Um projeto CtrlAltVersœ</div>
      HTML;

      if (!wp_mail($to, "[{$sitename}] Olá Viajante", $message, [
         'Content-Type: text/html; charset=UTF-8',
      ])) {
      }
   }

   public function set_author_query($query)
   {
      if (!$query->is_main_query() || !$query->is_author()) {
         return;
      }

      $query->set('post_type', ['text']);
   }

   public function set_retrieve_password_url($url, $path, $scheme)
   {
      if ('login' !== $scheme && !str_starts_with($path, 'wp-login.php?login=')) {
         return $url;
      }

      $url_parsed = wp_parse_url($path);

      if (empty($url_parsed['query'])) {
         return $url;
      }

      $args = [];
      wp_parse_str($url_parsed['query'], $args);

      if ('rp' !== $args['action']) {
         return $url;
      }

      return add_query_arg($args, home_url());
   }
}
