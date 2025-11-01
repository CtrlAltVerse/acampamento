<?php

use cavWP\Models\Post;

$color = '';
$text  = new Post($args['text'] ?? null);
$terms = $text->get('terms', taxonomy: 'club');

if (!empty($terms)) {
   $term  = $terms[0];
   $color = $term->get('color');
}

$is_series = empty($args['series_item']) && $text->get('menu_order') === 1;

$is_actual = is_singular('text') && get_the_ID() === $text->ID;

$full = !($args['small'] ?? false);

$link    = $text->get('link');
$name    = $text->get('name');
$summary = $text->get('summary', apply_filter: false);

if ($is_series && 'publish' === $text->get('status')) {
   $series = $text->get('terms', taxonomy: 'series');
   $serie  = $series[0];

   $link    = $serie->get('link');
   $name    = $serie->get('name');
   $summary = $serie->get('description');
}

if ('publish' === $text->get('status')) { ?>
<a href="<?php echo $link; ?>" class="flex flex-col gap-1.5">
   <div class="relative flex items-center justify-center aspect-card rounded-md border-4"
        style="border-color: <?php echo $color; ?>;background-color: <?php echo $color; ?>">
      <?php if ($text->get('image_mini', default: false)) { ?>
      <img class="absolute inset-0 rounded-md size-full object-cover object-center"
           src="<?php echo $text->get('image_mini'); ?>"
           title="<?php echo $name; ?>"
           loading="lazy"
           alt="" />
      <?php } ?>
      <?php if ($is_series) { ?>
      <span
            class="absolute top-3 right-3 rounded py-1 px-2 uppercase font-extrabold text-sm text-neutral-100"
            style="background-color: <?php echo $color; ?>">
         Série
      </span>
      <?php } ?>
      <?php if ($is_actual) { ?>
      <span
            class="absolute top-3 right-3 rounded py-1 px-2 uppercase font-extrabold text-sm bg-neutral-100 text-neutral-900">
         Atual
      </span>
      <?php } ?>
      <?php if (!empty($term)) { ?>
      <span class="font-bold uppercase text-md text-neutral-100">
         <?php echo $name; ?>
      </span>
      <span class="absolute bottom-0 left-0 flex items-center justify-center rounded-tr-2xl pt-1 pr-1 size-10 text-lg text-neutral-100"
            style="background-color: <?php echo $color; ?>">
         <?php echo $term->get('icon'); ?>
      </span>
      <?php } ?>
   </div>
   <div class="grow flex flex-col justify-end gap-1">
      <h3 class="line-clamp-3 font-semibold text-lg"
          title="<?php echo $name; ?>">
         <?php echo $name; ?>
      </h3>
      <p class="font-medium text-md">
         <?php echo $text->get('author:display_name'); ?>
      </p>
      <?php if ($full) { ?>
      <p class="line-clamp-4 font-serif mt-2"
         title="<?php echo $summary; ?>">
         <?php echo $summary; ?>
      </p>
      <?php } ?>
   </div>
</a>
<?php } ?>
<?php if ('future' === $text->get('status')) { ?>
<div class="flex flex-col gap-1.5">
   <div class="relative flex items-center justify-center aspect-card rounded-md border-4"
        style="border-color: <?php echo $color; ?>;background-color: <?php echo $color; ?>">
      <?php if (!empty($term)) { ?>
      <span class="flex flex-col text-center font-bold uppercase text-md text-neutral-100">
         <i class="ri-calendar-schedule-fill text-xl"></i>
         <span>Disponível em</span>
         <span><?php echo $text->get('published', format: 'date'); ?></span>
         <span><?php echo $text->get('published', format: 'time'); ?></span>
      </span>
      <span class="absolute bottom-0 left-0 flex items-center justify-center rounded-tr-2xl pt-1 pr-1 size-10 text-lg text-neutral-100"
            style="background-color: <?php echo $color; ?>">
         <?php echo $term->get('icon'); ?>
      </span>
      <?php } ?>
   </div>
   <div class="grow flex flex-col justify-end gap-1">
      <h3 class="line-clamp-3 font-semibold text-lg"
          title="<?php echo $name; ?>">
         <?php echo $name; ?>
      </h3>
      <p class="font-medium text-md">
         <?php echo $text->get('author:display_name'); ?>
      </p>
   </div>
</div>
<?php } ?>
