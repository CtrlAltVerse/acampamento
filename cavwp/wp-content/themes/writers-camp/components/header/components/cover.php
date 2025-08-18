<?php

use cavWP\Models\User;

?>
<div class="flex justify-end">
   <button class="py-2 px-3 cursor-pointer" type="button" x-on:click.prevent="toggleTheme()">
<i class="ri-moon-fill dark:hidden"></i>
<i class="ri-sun-line hidden dark:inline"></i>
   </button>
   <button class="flex items-center gap-1 cursor-pointer" type="button" x-on:click.prevent="openMenu">
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
