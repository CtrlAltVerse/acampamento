<?php get_component('header'); ?>
<main class="main flex flex-col gap-25">
   <?php get_page_component(__FILE__, 'intro'); ?>

   <?php get_component( 'clubs'); ?>

   <?php get_page_component(__FILE__, 'features'); ?>

   <?php get_page_component(__FILE__, 'challenges'); ?>
</main>
<?php get_component('footer'); ?>
