<?php

use writersCampP\Club\Utils;

$clubs = Utils::get();

?>
<section>
   <hgroup class="mb-6">
      <h2 class="h2">
         <?php echo get_taxonomy('club')->label; ?>
      </h2>
   </hgroup>
   <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-3 gap-y-6">
      <?php foreach ($clubs as $club) { ?>
      <a class="relative flex items-center mx-auto w-full min-h-55 text-neutral-100 overflow-hidden"
         href="<?php echo $club->get('link'); ?>">
         <div class="relative z-0 mr-25 section-border border-brown-700 w-full"
              style="background-color: <?php echo $club->get('color'); ?>">
            <div class="flex flex-col gap-6 mr-25 text-shadow-lg">
               <h3 class="font-extrabold uppercase text-lg sm:text-2xl lg:text-lg xl:text-2xl">
                  <?php echo $club->get('name'); ?>
               </h3>
               <p class="font-medium text-lg">
                  <?php echo nl2br($club->get('description')); ?>
               </p>
               <p class="font-bold text-lg sm:text-xl lg:text-lg xl:text-xl">
                  <?php echo $club->get('genres'); ?>
               </p>
            </div>
         </div>
         <?php echo $club->get('seal', image_size: 'medium', image_attrs: [
            'class' => 'absolute right-0 object-cover size-55',
         ]); ?>
      </a>
      <?php } ?>
   </div>
</section>
