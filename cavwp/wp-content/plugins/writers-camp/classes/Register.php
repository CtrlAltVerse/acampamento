<?php

namespace writersCampP;

use cavWP\Utils;

class Register
{
   public function __construct()
   {
      add_action('after_setup_theme', [$this, 'register_menus']);
      add_action('pre_get_posts', [$this, 'set_search_query']);
      add_action('template_redirect', [$this, 'block_dashboard']);

      add_filter('get_custom_logo', [$this, 'set_logo']);
      add_filter('get_search_query', [$this, 'clean_search']);
      add_filter('posts_where', [$this, 'filter_query'], 10, 2);

      new Register_Gamification();
      new Register_Shortcodes();
   }

   public function block_dashboard()
   {
      if (!is_page(['dashboard', 'profile', 'publish'])) {
         return;
      }

      if (is_user_logged_in()) {
         $email_not_verified = get_user_meta(get_current_user_id(), 'email_not_verified', true);

         if (!empty($email_not_verified) && is_page(['profile', 'publish'])) {
            if (wp_safe_redirect(home_url('dashboard'))) {
               exit;
            }
         }
      } else {
         if (wp_safe_redirect(home_url('?action=signin'))) {
            exit;
         }
      }
   }

   public function clean_search($text)
   {
      $text = trim($text);

      return substr($text, 0, 66);
   }

   public function filter_query($where, $query)
   {
      if (empty($query->query_vars['menu_order']) && empty($query->query['menu_order'])) {
         return $where;
      }

      global $wpdb;

      $posts_table = $wpdb->posts;

      return str_replace($posts_table . '.menu_order = 99', $posts_table . '.menu_order BETWEEN 0 AND 1', $where);
   }

   public function register_menus(): void
   {
      register_nav_menus([
         'header'  => esc_html__('Cabeçalho', 'cavcamp'),
         'profile' => esc_html__('Usuário', 'cavcamp'),
         'footer'  => esc_html__('Rodapé', 'cavcamp'),
      ]);
   }

   public function set_logo($logo)
   {
      if (!empty($logo)) {
         return $logo;
      }

      return Utils::render_svg(get_template_directory() . '/assets/vectors/mini-icon.svg', 'h-8');
   }

   public function set_search_query($query)
   {
      if (!$query->is_main_query() || !is_search()) {
         return;
      }

      $query->set('post_type', ['text']);

      $term = get_search_query();

      if (empty($term)) {
         $query->set('post__in', [0]);
      }
   }
}
