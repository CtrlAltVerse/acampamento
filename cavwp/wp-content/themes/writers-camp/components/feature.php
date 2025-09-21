<?php

use cavWP\Models\Post;

$color = '';
$text  = new Post($args['text'] ?? null);
$terms = $text->get('terms', taxonomy: 'club');

if (!empty($terms)) {
   $term  = $terms[0];
   $color = $term->get('color');
}

$full = !($args['small'] ?? false);

?>
<a href="<?php echo $text->get('link'); ?>"
   class="flex flex-col gap-1.5">
   <div class="relative flex items-center justify-center aspect-card rounded-md border-4"
        style="border-color: <?php echo $color; ?>;background-color: <?php echo $color; ?>">
      <?php if ($text->get('image_mini', default: false)) { ?>
      <img class="absolute inset-0 rounded-md size-full object-cover object-center"
           src="<?php echo $text->get('image_mini'); ?>"
           title="<?php echo $text->get('name'); ?>"
           loading="lazy"
           alt="" />
      <?php } ?>
      <?php if (!empty($term)) { ?>
      <span class="font-bold uppercase text-md text-neutral-100">
         <?php echo $term->get('name'); ?>
      </span>
      <span class="absolute bottom-0 left-0 flex items-center justify-center rounded-tr-2xl pt-1 pr-1 size-10 text-lg text-neutral-100"
            style="background-color: <?php echo $color; ?>">
         <?php echo $term->get('icon'); ?>
      </span>
      <?php } ?>
   </div>
   <div class="grow flex flex-col justify-end gap-1">
      <h3 class="line-clamp-3 font-semibold text-lg"
          title="<?php echo $text->get('name'); ?>">
         <?php echo $text->get('name'); ?>
      </h3>
      <p class="font-medium text-md">por
         <?php echo $text->get('author:display_name'); ?>
      </p>
      <?php if ($full) { ?>
      <p class="line-clamp-4 font-serif mt-2"
         title="<?php echo $text->get('summary', apply_filter: false); ?>">
         <?php echo $text->get('summary', apply_filter: false); ?>
      </p>
      <?php } ?>
   </div>
</a>
