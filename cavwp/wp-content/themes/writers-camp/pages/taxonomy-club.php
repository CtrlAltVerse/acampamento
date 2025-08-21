<?php

use cavWP\Models\Term;

get_component('header');

$club = new Term();

?>
<div class="relative py-6"
     style="background-color: <?php echo $club->get('color'); ?>">
     <div class="absolute inset-0 z-0 bg-neutral-900/20"></div>
   <div class="container relative flex flex-col justify-between min-h-55 text-neutral-100 text-shadow-lg">
      <?php echo $club->get('seal', image_size: 'medium', image_attrs: [
         'class' => 'absolute top-0 right-3 z-0 object-cover size-55',
      ]); ?>
      <hgroup class="flex flex-col gap-3 relative z-1">
      <h3 class="font-extrabold uppercase text-xl md:text-3xl">
         <?php echo $club->get('name'); ?>
      </h3>
      <p class="font-medium text-lg md:text-2xl">
         <?php echo $club->get('genres'); ?>
      </p>
      </hgroup>
      <p class="relative z-1 font-medium text-lg md:text-xl max-w-180">
         <?php echo $club->get('description'); ?>
      </p>
   </div>
</div>
<?php if (have_posts()) { ?>
<main class="main">
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
