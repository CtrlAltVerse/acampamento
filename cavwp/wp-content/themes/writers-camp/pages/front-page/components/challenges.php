<?php

use writersCampP\Challenge\Utils as UtilsChallenge;

?>
<section>
   <div class="flex items-center justify-between mb-6">
      <hgroup class="flex items-center">
         <h2 class="h2">Desafios em aberto</h2>
      </hgroup>
      <a class="btn" href="<?php echo get_post_type_archive_link('challenge'); ?>">
         Mais desafios
      </a>
   </div>
   <ul class="flex flex-col gap-5">
      <?php $challenges = UtilsChallenge::get(); ?>
      <?php foreach ($challenges as $challenge) { ?>
      <li>
         <?php get_component('challenge', ['challenge' => $challenge]); ?>
      </li>
      <?php } ?>
   </ul>
</section>
