<?php

use writersCampP\Text\Utils;

$texts = Utils::get(6, 'recent');

?>
<?php get_component('header'); ?>
<main class="main flex flex-col gap-25">
   <section>
      <hgroup class="flex flex-col gap-3 mb-9">
         <h1 class="h1"><i class="ri-error-warning-line"></i> Página não encontrada</h1>
         <p>Esta página foi apagada, movida ou nunca existiu. Tente uma busca ou acessar os conteúdos mais recentes.</p>
      </hgroup>
      <div class="text-2xl mb-9 rounded border border-neutral-500">
         <?php get_component('searchform'); ?>
      </div>
   </section>
   <?php get_component('clubs'); ?>
   <?php if (!empty($texts)) { ?>
   <section class="flex flex-col gap-6">
      <h2 class="h2">Publicações mais recentes</h2>
      <ul class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-3 gap-y-7">
         <?php foreach ($texts as $text) { ?>
         <li>
            <?php get_component('feature', ['text' => $text]); ?>
         </li>
         <?php } ?>
      </ul>
   </section>
   <?php } ?>
</main>
<?php get_component('footer'); ?>
