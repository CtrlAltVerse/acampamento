<?php

use cavWP\Models\Term;

get_component('header');

$series = new Term();

$first = 0;
$clubs = [];
$authors = [];

if (have_posts()) {
   global $wp_query;

   foreach ($wp_query->posts as $series_post) {
      if (empty($first)) {
         $first = get_post_meta($series_post->ID, 'image_full', true);
      }

      if (!in_array($series_post->post_author, array_keys($authors))) {
         $authors[$series_post->post_author] = get_the_author_meta('display_name', $series_post->post_author);
      }

      $terms = get_the_terms($series_post, 'club');
      $term = $terms[0];

      if (!in_array($term->term_id, array_keys($clubs))) {
         $clubs[$term->term_id] = new Term($term);
      }
   }
}

?>
<div class="relative py-6">
   <div class="absolute inset-0 z-1 bg-neutral-900/20"></div>
   <?php if (!empty($first)) { ?>
      <img class="absolute inset-0 z-0 size-full object-cover"
         src="<?php echo $first; ?>" loading="lazy" alt="">
   <?php } ?>
   <div class="container flex flex-col justify-between gap-3 min-h-55 text-neutral-100 text-shadow-lg">
      <div class="flex flex-col gap-3">
      <?php if (!empty($clubs)) { ?>
      <div class="relative flex gap-2">
         <?php foreach ($clubs as $club) { ?>
            <a href="<?php echo $club->get('link'); ?>"
               class="rounded py-1 px-2 uppercase font-extrabold text-sm text-neutral-100"
               style="background-color: <?php echo $club->get('color'); ?>">
               <?php echo $club->get('name'); ?>
            </a>
         <?php } ?>
         </div>
      <?php } ?>
      <hgroup class="flex flex-col gap-1.5 relative z-1">
         <h3 class="font-extrabold uppercase text-xl md:text-3xl">
            <?php echo $series->get('name'); ?>
         </h3>
         <?php if (!empty($authors)) { ?>
            <p class="font-medium text-md">
               Por
               <?php echo implode(', ', $authors); ?>
            </p>
         <?php } ?>
      </hgroup>
      </div>
      <p class="relative z-1 font-medium text-md sm:text-lg md:text-xl max-w-1/2 md:max-w-180">
         <?php echo $series->get('description'); ?>
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
