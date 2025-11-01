<?php

use cavWP\Models\Term;
use cavWP\Utils;

get_component('header');

$series = new Term();

$first   = 0;
$clubs   = [];
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
      $term  = $terms[0];

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
   <div class="container relative z-1 flex flex-col justify-between gap-3 min-h-55 text-neutral-100 text-shadow-lg">
      <div class="flex flex-col gap-3">
         <div class="relative flex gap-2">
            <span
                  class="rounded py-1 px-2 uppercase font-extrabold text-sm bg-neutral-100 text-neutral-900">
               Série
            </span>
            <?php if (!empty($clubs)) { ?>
            <?php foreach ($clubs as $club) { ?>
            <a href="<?php echo $club->get('link'); ?>"
               class="rounded py-1 px-2 uppercase font-extrabold text-sm text-neutral-100"
               style="background-color: <?php echo $club->get('color'); ?>">
               <?php echo $club->get('name'); ?>
            </a>
            <?php } ?>
            <?php } ?>
         </div>
         <hgroup class="flex flex-col gap-1.5 relative z-1">
            <h3 class="font-extrabold uppercase text-xl md:text-3xl">
               <?php echo $series->get('name'); ?>
            </h3>
            <?php if (!empty($authors)) { ?>
            <p class="font-medium text-md">
               <?php echo Utils::parse_titles($authors); ?>
            </p>
            <?php } ?>
         </hgroup>
      </div>
      <div class="flex flex-col items-start gap-3">
         <p class="font-medium text-md sm:text-lg md:max-w-180">
            <?php echo nl2br($series->get('description')); ?>
         </p>
         <?php if (have_rows('links', $series())) { ?>
         <ul class="flex rounded border border-neutral-100 divide-x">
            <?php while (have_rows('links', $series())) {
               the_row(); ?>
            <li>
               <a class="py-2 px-4"
                  href="<?php echo get_sub_field('url'); ?>"
                  target="_blank" rel="external nofollow">
                  <?php if (!empty(get_sub_field('icon'))) { ?>
                  <i
                     class="<?php echo get_sub_field('icon'); ?>"></i>
                  <?php } ?>
                  <?php echo get_sub_field('label'); ?>
               </a>
            </li>
            <?php } ?>
         </ul>
         <?php } ?>
      </div>
   </div>
</div>
<?php if (have_posts()) { ?>
<main class="main">
   <ul class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-3 gap-y-7">
      <?php while (have_posts()) { ?>
      <?php the_post(); ?>
      <li>
         <?php get_component('feature', ['series_item' => 1]); ?>
      </li>
      <?php } ?>
   </ul>
   <?php get_component('pagination'); ?>
</main>
<?php } ?>
<?php get_component('footer'); ?>
