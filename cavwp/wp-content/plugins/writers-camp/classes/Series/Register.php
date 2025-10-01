<?php

namespace writersCampP\Series;

final class Register
{
   public function __construct()
   {
      add_action('init', [$this, 'register'], 9);
      add_action('pre_get_posts', [$this, 'filter_posts']);
   }

   public function filter_posts($query)
   {
      if (is_admin() || !$query->is_main_query() || !$query->is_tax('series')) {
         return;
      }

      $query->set('posts_per_page', -1);
      $query->set('orderby', ['menu_order' => 'ASC', 'date' => 'desc']);
      $query->set('post_status', ['publish', 'future']);
   }

   public function register()
   {
      register_taxonomy('series', 'text', [
         'labels' => [
            'name'          => 'Séries',
            'singular_name' => 'Série',
            'edit_item'     => 'Editar série',
         ],
         'hierarchical'      => true,
         'public'            => true,
         'show_in_nav_menus' => false,
         'show_tagcloud'     => false,
         'show_in_rest'      => true,
         'show_admin_column' => true,
      ]);
   }
}
