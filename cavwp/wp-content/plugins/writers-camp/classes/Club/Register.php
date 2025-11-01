<?php

namespace writersCampP\Club;

use cavWP\Models\Post;
use cavWP\Models\Term;

class Register
{
   public function __construct()
   {
      add_action('init', [$this, 'register'], 9);
      add_action('wp_enqueue_scripts', [$this, 'set_color'], 11);
      add_action('pre_get_posts', [$this, 'set_query']);

      add_filter('cavwp_term_get', [$this, 'filter_name'], 10, 4);
   }

   public function filter_name($value, $key, $term)
   {
      if ('name' !== $key || is_bool($value)) {
         return $value;
      }

      $icon = get_term_meta($term->term_id, 'icon', true);

      return $icon . ' ' . $value;
   }

   public function register(): void
   {
      register_taxonomy('club', 'text', [
         'labels' => [
            'name'          => 'Guildas',
            'singular_name' => 'Guilda',
            'edit_item'     => 'Editar guilda',
         ],
         'hierarchical'      => false,
         'public'            => true,
         'show_in_nav_menus' => false,
         'show_tagcloud'     => false,
         'show_in_rest'      => true,
         'show_admin_column' => true,
         'rewrite'           => [
            'slug' => 'guilda',
         ],
      ]);
   }

   public function set_color()
   {
      if (is_tax('club')) {
         $Term  = new Term();
         $color = $Term->get('color');
      }

      if (is_singular('text')) {
         $Post  = new Post();
         $clubs = $Post->get('terms', taxonomy : 'club');

         if (!empty($clubs)) {
            $color = get_term_meta($clubs[0]->term_id, 'color', true);
         }
      }

      if (empty($color)) {
         $color = 'var(--color-brown-400)';
      }

      wp_add_inline_style('main', ":root{--color-guild: {$color}}");
   }

   public function set_query($query)
   {
      if (is_admin() || !$query->is_main_query() || !$query->is_tax('club')) {
         return;
      }

      $query->set('menu_order', 99);
   }
}
