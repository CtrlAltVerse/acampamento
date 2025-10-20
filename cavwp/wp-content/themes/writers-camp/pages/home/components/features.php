<?php

use writersCampP\Text\Utils;

$texts = Utils::get(6);

?>
<?php if (!empty($texts)) { ?>
   <section>
      <div class="flex flex-col sm:flex-row gap-1 justify-between items-start sm:items-center mb-6">
         <hgroup class="flex flex-col gap-1">
            <h2 class="h2">Destaques</h2>
            <p>Mais populares das últimas semanas.</p>
         </hgroup>
         <a class="btn"
            href="<?php echo get_post_type_archive_link('text'); ?>">
            Todos os textos
         </a>
      </div>
      <ul class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-3 gap-y-6">
         <?php foreach ($texts as $text) { ?>
            <li>
               <?php get_component('feature', ['text' => $text]); ?>
            </li>
         <?php } ?>
      </ul>
   </section>
<?php } ?>
