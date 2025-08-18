<?php

get_component('header');

$term = get_search_query();

?>
<main class="main">
   <h1 class="h1 mb-9">Busca</h1>
   <div class="text-2xl mb-9 rounded border border-neutral-500">
      <?php get_component('searchform'); ?>
   </div>
   <?php if (have_posts()) { ?>
   <ul class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-3 gap-y-7">
      <?php while (have_posts()) { ?>
      <?php the_post(); ?>
      <li>
         <?php get_component('feature'); ?>
      </li>
      <?php } ?>
   </ul>
   <?php get_component('pagination'); ?>
   <?php } ?>
</main>
<?php get_component('footer'); ?>
