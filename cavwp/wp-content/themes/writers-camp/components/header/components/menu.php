<?php

use cavWP\Models\User;
use cavWP\Utils as CavUtils;
use writersCampP\Club\Utils as ClubUtils;
use writersCampP\Utils;

$clubs = ClubUtils::get();

?>
<dialog id="menu"
        class="ml-auto w-full max-w-xs max-h-dvh h-full bg-neutral-100 dark:bg-neutral-700 backdrop:bg-neutral-900/60"
        x-on:click.self="closeMenu">
   <div class="h-full">
      <?php if (is_user_logged_in()) { ?>
      <?php $User = new User(get_current_user_id()); ?>
      <?php $rank = Utils::get_rank($User->get('xp', default: 0)); ?>
      <div class="<?php echo $rank->color; ?>">
         <div class="flex items-center bg-neutral-900/10 hover:bg-neutral-300/50">
            <a class="shrink-0 flex !pr-0"
               href="<?php echo $User->get('link'); ?>">
               <?php echo $User->get('avatar', 48, [
                  'class' => 'size-12 rounded border border-current',
               ]); ?>
            </a>
            <a class="grow flex flex-col"
               href="<?php echo $User->get('link'); ?>">
               <span class="font-medium text-ellipsis text-md">
                  <?php echo $User->get('name'); ?>
               </span>
               <span class="text-current/80 text-sm">
                  @<?php echo $User->get('slug'); ?>
               </span>
            </a>
            <button class="shrink-0 flex justify-center items-center mr-3 rounded bg-neutral-300/10 hover:bg-neutral-100 hover:text-neutral-700 focus-visible:bg-neutral-300 size-8 text-md cursor-pointer"
                    type="button" x-on:click.prevent="closeMenu">
               <i class="ri-close-fill"></i>
            </button>
         </div>
         <hr class="border-neutral-700/50 dark:border-neutral-100/50" />
         <a class="flex gap-2.5 items-center font-normal"
            href="<?php echo Utils::get_page_link('guide'); ?>">
            <span class="rounded-full px-2 bg-neutral-100 text-neutral-700">
               <?php echo $User->get('xp', default: 0); ?>
               XP
            </span>
            <?php echo $rank->label; ?>
         </a>
      </div>
      <?php } else {  ?>
      <div class="bg-neutral-500 text-neutral-100">
         <div class="flex items-center  min-h-17 bg-neutral-900/10 hover:bg-neutral-300/50">
            <button class="grow text-left cursor-pointer py-2.5 px-3" type="button"
                    x-on:click.prevent="$store.login.method='intro';login.showModal()">
               <i class="ri-login-box-fill ri-fw"></i> Inscrições
            </button>
            <button class="shrink-0 flex justify-center items-center mr-3 rounded bg-neutral-300/10 hover:bg-neutral-100 hover:text-neutral-700 focus-visible:bg-neutral-300 size-8 text-md cursor-pointer"
                    type="button" x-on:click.prevent="closeMenu">
               <i class="ri-close-fill"></i>
            </button>
         </div>
      </div>
      <?php } ?>
      <div class="dark:text-neutral-100">
         <ul>
            <li>
               <?php get_component('searchform'); ?>
            </li>
         </ul>
         <?php if (current_user_can('manage_options')) { ?>
         <ul>
            <li>
               <a href="<?php echo admin_url(); ?>">
                  Administração
               </a>
            </li>
         </ul>
         <?php } ?>
         <?php if (is_user_logged_in()) { ?>
         <?php wp_nav_menu([
            'theme_location' => 'profile',
            'container'      => '',
            'menu_id'        => '',
            'menu_class'     => '',
         ]); ?>
         <?php } ?>
         <hr class="border-neutral-700/50 dark:border-neutral-100/50" />
         <?php wp_nav_menu([
            'theme_location' => 'footer',
            'container'      => '',
            'menu_id'        => '',
            'menu_class'     => '',
         ]); ?>
         <hr class="border-neutral-700/50 dark:border-neutral-100/50" />
         <ul>
            <?php foreach ($clubs as $club) { ?>
            <li>
               <a class="flex gap-2"
                  href="<?php echo $club->get('link'); ?>"
                  title="<?php echo $club->get('description', apply_filter: false); ?>">
                  <?php echo $club->get('name'); ?>
               </a>
            </li>
            <?php } ?>
         </ul>
         <?php if (is_user_logged_in()) { ?>
         <hr class="border-neutral-700/50 dark:border-neutral-100/50" />
         <a class="flex gap-2"
            href="<?php echo wp_logout_url(CavUtils::get_current_url()); ?>">
            <i class="ri-logout-circle-r-line ri-fw"></i>
            Sair
         </a>
         <?php } ?>
      </div>
   </div>
</dialog>
