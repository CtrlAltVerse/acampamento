<?php

$challenge = $args['challenge'];
$complete  = $args['complete'] ?? false;

$margin      = '';
$ribbon      = 'quest';
$ribbon_mark = '';

if ($complete) {
   $margin      = '-mb-1.75';
   $ribbon      = 'quest-complete';
   $ribbon_mark = '<span><i class="ri-check-fill"></i></span>';
}

?>
<div class="aspect-quest flex flex-col gap-3.5 section-border border-brown-500 bg-brown-50 dark:bg-brown-200">
   <div class="flex <?php echo $margin; ?>">
      <span class="<?php echo $ribbon; ?> shrink-0">
         <?php echo $ribbon_mark; ?>
      </span>
      <h3 class="quest-name before:bg-brown-50 dark:before:bg-brown-200">
         <?php echo $challenge->get('title'); ?>
      </h3>
   </div>
   <div class="w-full flex flex-col gap-3.5">
      <div class="text-lg whitespace-pre-line">
         <?php echo $challenge->get('summary', apply_filter: false); ?>
      </div>
      <div class="flex flex-wrap v-gap-3">
         <?php $texts = $challenge->get('texts', default: []); ?>
         <?php foreach (range(0, 3) as $key) {
            get_component(['challenge', 'card'], [
               'key'       => $key,
               'text'      => $texts[$key] ?? false,
               'challenge' => $challenge,
            ]);
         } ?>
      </div>
   </div>
</div>
