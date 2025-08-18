<?php

use cavWP\Models\Post;
use cavWP\Utils;
use writersCampP\Challenge\Utils as UtilsChallenge;

get_component('header');

?>
<div class="main">
   <main>
      <h1 class="sr-only">Desafios</h1>
      <?php if (1 === Utils::get_page()) { ?>
      <section>
         <div class="flex items-center justify-between">
            <hgroup class="mb-9">
               <h2 class="h2">Desafios abertos</h2>
               <p>A medida que desafios são preenchidos, novos são desbloqueados.</p>
            </hgroup>
         </div>
         <ul class="flex flex-col gap-5">
            <?php $challenges = UtilsChallenge::get(5); ?>
            <?php foreach ($challenges as $challenge) { ?>
            <li>
               <?php get_component('challenge', ['challenge' => $challenge]); ?>
            </li>
            <?php } ?>
         </ul>
      </section>
      <?php } ?>
      <?php if (have_posts()) { ?>
      <section>
         <div class="flex items-center justify-between">
            <hgroup class="mt-25 mb-9">
               <h2 class="h2">Desafios completos</h2>
            </hgroup>
         </div>
         <ul class="flex flex-col gap-5">
            <?php while (have_posts()) { ?>
            <?php the_post(); ?>
            <li>
               <?php get_component('challenge', ['challenge' => new Post(), 'complete' => true]); ?>
            </li>
            <?php } ?>
         </ul>
      </section>
      <?php } ?>
   </main>
   <?php get_component('pagination'); ?>
</div>

<?php get_component('footer'); ?>
