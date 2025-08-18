<?php

do_action('get_header');

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
   <?php wp_head(); ?>
</head>

<body id="top" x-data="bonfire" <?php body_class('select-none bg-yellow-100 text-neutral-700 dark:text-neutral-100 dark:bg-neutral-700 text-md'); ?>>
   <?php wp_body_open(); ?>

   <nav class="absolute top-3 left-3 z-20">
      <ul>
         <li><a class="btn not-focus:sr-only" href="#main">Pular ao conteúdo</a></li>
      </ul>
   </nav>

   <div class="relative z-10 bg-guild text-neutral-100 text-lg shadow-lg">
      <header class="grid grid-cols-3 py-4 px-3" data-nosnippet>
         <div class="flex items-center gap-3 col-span-2 lg:col-span-1">
            <?php the_custom_logo(); ?>
            <a href="<?php echo home_url(); ?>"
               class="font-semibold">
               <?php bloginfo('name'); ?>
            </a>
         </div>
         <?php wp_nav_menu([
            'theme_location' => 'header',
            'container'      => '',
            'menu_id'        => '',
            'menu_class'     => 'hidden lg:flex justify-center items-center gap-4',
         ]); ?>
         <div class="menu-user">
         <?php get_component(['header', 'cover']); ?>
         </div>
      </header>
   </div>
   <?php get_component(['header', 'menu']); ?>
   <?php if (!is_user_logged_in()) { ?>
   <?php get_component(['header', 'login']); ?>
   <?php } ?>
