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
   <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-6">
      <?php foreach ($clubs as $club) { ?>
      <a class="relative flex mx-auto w-full min-h-55 text-neutral-100"
         href="<?php echo $club->get('link'); ?>">
         <div class="relative z-0 flex flex-col items-center lg:items-start lg:mr-25 section-border border-brown-700 w-full h-full"
              style="background-color: <?php echo $club->get('color'); ?>">
            <?php echo $club->get('seal', image_size: 'medium', image_attrs: [
               'class' => '-mt-9 mb-3 lg:m-0 lg:absolute -top-3 -right-25 object-cover size-55',
            ]); ?>
            <div class="flex flex-col justify-between gap-6 lg:mr-25 text-shadow-lg">
               <hgroup class="flex flex-col gap-1 lg:gap-3 text-center lg:text-left">
                  <h3 class="font-extrabold uppercase text-xl sm:text-lg md:text-xl lg:text-lg xl:text-2xl">
                     <?php echo $club->get('name'); ?>
                  </h3>
                  <p class="font-bold text-xl sm:text-lg md:text-xl lg:text-lg xl:text-xl">
                     <?php echo $club->get('genres'); ?>
                  </p>
               </hgroup>
               <p class="font-medium text-lg text-balance">
                  <?php echo nl2br($club->get('description')); ?>
               </p>
            </div>
         </div>
      </a>
      <?php } ?>
   </div>
</section>
