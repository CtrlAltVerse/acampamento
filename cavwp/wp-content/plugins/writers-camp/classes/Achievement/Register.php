<?php

namespace writersCampP\Achievement;

class Register
{
   public function __construct()
   {
      add_action('init', [$this, 'register']);
   }

   public function register(): void
   {
      register_post_type('achievement', [
         'labels' => [
            'name'          => 'Insígnias',
            'archives'      => 'Insígnias',
            'singular_name' => 'Insígnia',
         ],
         'public'            => false,
         'has_archive'       => false,
         'show_ui'           => true,
         'show_in_menu'      => true,
         'show_in_admin_bar' => true,
         'menu_position'     => 3,
         'menu_icon'         => 'dashicons-star-filled',
         'supports'          => ['title', 'excerpt'],
      ]);
   }
}
