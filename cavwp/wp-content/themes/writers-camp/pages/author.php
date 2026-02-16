<?php

use cavWP\Models\Term;
use cavWP\Models\User;

get_component('header');

$club   = new Term();
$writer = new User();

$site_url = $writer->get('user_url');
$socials  = $writer->get('socials');

?>
<div class="py-6 bg-brown-100 text-neutral-700">
   <div class="container flex flex-col lg:flex-row items-center justify-between gap-9 min-h-55">
      <div class="shrink-0 lg:w-1/2 relative flex gap-4 lg:gap-9">
         <?php get_component('level'); ?>
         <div class="flex flex-col justify-between pt-5 pb-11">
            <div class="flex flex-col gap-1">
               <span class="text-2xl font-semibold">
                  <?php echo $writer->get('name'); ?>
               </span>
               <span
                     class="text-lg">@<?php echo $writer->get('slug'); ?></span>
            </div>
            <ul class="flex gap-2 lg:gap-4 flex-wrap mt-8 text-xl">
               <?php if (!empty($site_url)) { ?>
               <li>
                  <a href="<?php echo $site_url; ?>"
                     target="_blank" rel="external" title="Site pessoal">
                     <i class="ri-global-line"></i>
                  </a>
               </li>
               <?php } ?>
               <?php if (!empty($socials)) { ?>
               <?php foreach ($socials as $social) { ?>
               <li>
                  <a href="<?php echo $social['profile']; ?>"
                     title="<?php $social['name']; ?>"
                     target="_blank" rel="external">
                     <i
                        class="<?php echo $social['icon']; ?>"></i>
                  </a>
               </li>
               <?php ?>
               <?php } ?>
               <?php } ?>
            </ul>
         </div>
      </div>
      <div class="grow">
         <?php echo nl2br($writer->get('description')); ?>
      </div>
   </div>
</div>

<?php if (have_posts()) { ?>
<main class="main">
   <h1 class="h1 mb-9">Textos de
      <?php echo $writer->get('name'); ?>
   </h1>
   <ul class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-3 gap-y-7">
      <?php while (have_posts()) { ?>
      <?php the_post(); ?>
      <li>
         <?php get_component('feature'); ?>
      </li>
      <?php } ?>
   </ul>
   <?php get_component('pagination'); ?>
</main>
<?php } ?>
<?php get_component('footer'); ?>
