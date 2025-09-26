<?php

use cavWP\Models\User;

?>
<div class="flex justify-end items-center">
   <button class="p-2 cursor-pointer" x-show="bookmark!==null&&bookmark.url.length" x-on:click.prevent="openBookmark()" title="Abrir marca-página" type="button" x-cloak>
      <i class="ri-bookmark-fill"></i>
   </button>
   <button class="p-2 cursor-pointer" type="button" x-on:click.prevent="toggleTheme()">
      <i class="ri-moon-fill dark:hidden"></i>
      <i class="ri-sun-line hidden dark:inline"></i>
   </button>
   <button class="p-2 cursor-pointer" type="button" x-on:click.prevent="openMenu">
   <?php if (is_user_logged_in()) { ?>
   <?php $User = new User(get_current_user_id()); ?>
      <?php echo $User->get('avatar', 32, [
         'class' => 'rounded',
      ]); ?>
   <?php } else { ?>
      <i class="ri-menu-fill"></i>
   </button>
   <?php } ?>
</div>
