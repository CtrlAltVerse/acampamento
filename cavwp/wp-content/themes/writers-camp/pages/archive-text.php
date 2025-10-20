<?php

get_component('header');

?>
<?php if (have_posts()) { ?>
<main class="main">
   <h1 class="h1 mb-9">Todos os textos</h1>
   <ul class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-3 gap-y-7">
      <?php while (have_posts()) { ?>
      <?php the_post(); ?>
      <li>
         <?php get_component('feature'); ?>
      </li>
      <?php } ?>
   </ul>
   <?php get_component('pagination'); ?>
</main>
<?php } ?>
<?php get_component('footer'); ?>
