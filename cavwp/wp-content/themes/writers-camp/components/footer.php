<?php

use cavWP\Utils as CavUtils;
use writersCampP\Club\Utils;

$clubs = Utils::get();

?>
<div class="relative bg-linear-to-b from-brown-400 from-22% to-brown-700 to-44% mt-55 pt-20">
   <div class="footer-fullname flex items-center gap-2">
      <?php CavUtils::render_svg(get_template_directory() . '/assets/vectors/fire.svg', 'size-[11vw]'); ?>
      <?php bloginfo('name'); ?>
   </div>
   <footer class="container text-neutral-100">
      <div class="flex flex-col xl:flex-row justify-between gap-9">
         <div class="flex flex-col gap-4 justify-between">
            <div class="flex items-center gap-3">
               <?php the_custom_logo(); ?>
               <h2 class="font-semibold text-lg">
                  <a
                     href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>
               </h2>
            </div>
            <?php wp_nav_menu([
               'theme_location' => 'footer',
               'menu_id'        => '',
               'menu_class'     => 'flex gap-4',
            ]); ?>
         </div>
         <ul class="grow max-w-4xl grid grid-cols-2 grid-rows-auto lg:grid-cols-4 gap-x-3 gap-y-6">
            <?php foreach ($clubs as $club) { ?>
            <li class="flex flex-col gap-4">
               <h3 class="font-semibold text-lg">
                  <a
                     href="<?php echo $club->get('link'); ?>">
                     <?php echo $club->get('name'); ?>
                  </a>
               </h3>
               <a class="font-medium"
                  href="<?php echo $club->get('link'); ?>">
                  <?php echo nl2br($club->get('genres')); ?>
               </a>
            </li>
            <?php } ?>
         </ul>
      </div>
   </footer>
   <div class="relative">
      <?php CavUtils::render_svg(get_template_directory() . '/assets/vectors/deep-footer.svg'); ?>
      <div class="absolute inset-0 w-full flex items-end justify-center pb-8">
         <a class="text-2xl" href="https://ctrl.altvers.net" target="_blank"
            title="Este projeto faz parte de CtrlAltVersœ"
            rel="external">🌌</a>
      </div>
   </div>
</div>

<?php wp_footer(); ?>
</body>

</html>
