<?php

use cavWP\Utils as CavWPUtils;
use writersCampP\Utils;

?>
<section class="flex flex-col items-center gap-9 lg:flex-row">
   <?php CavWPUtils::render_svg(get_template_directory() . '/assets/vectors/logo.svg', 'hidden lg:block size-full max-w-xl'); ?>
   <div class="flex-1 flex flex-col justify-between items-start gap-9">
      <hgroup class="flex-1 flex flex-col gap-3">
         <h1 class="h1">
            <?php bloginfo('name'); ?>
         </h1>
         <p class="text-2xl font-serif font-semibold">
            <?php bloginfo('description'); ?>
         </p>
      </hgroup>
      <div class="font-serif text-xl w-full max-w-200">
         <?php the_content(); ?>
      </div>
      <div class="flex gap-4">
         <a class="btn"
            href="<?php echo Utils::get_page_link('guide'); ?>">Primeiros
            Passos</a>
         <?php if (is_user_logged_in()) { ?>
         <a class="btn"
            href="<?php echo Utils::get_page_link('hints'); ?>">Dicas</a>
         <?php } else { ?>
         <button class="btn" type="button" x-on:click.prevent="$store.login.method='intro';login.showModal()">Entrar</button>
         <?php } ?>
      </div>
   </div>
   <?php CavWPUtils::render_svg(get_template_directory() . '/assets/vectors/logo.svg', 'lg:hidden block size-full max-w-xl'); ?>
</section>
