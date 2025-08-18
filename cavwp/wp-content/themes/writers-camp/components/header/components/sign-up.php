<?php

use cavWP\Models\User;

?>
<div class="flex justify-end">
   <button class="py-2 px-3 cursor-pointer" type="button" x-on:click.prevent="toggleTheme()">
<i class="ri-moon-fill dark:hidden"></i>
<i class="ri-sun-line hidden dark:inline"></i>
   </button>
   <?php if (is_user_logged_in()) { ?>
   <?php $User = new User(get_current_user_id()); ?>
   <button class="flex items-center gap-1 cursor-pointer" type="button" x-on:click.prevent="openMenu">
      <?php echo $User->get('avatar', 32, [
         'class' => 'rounded',
      ]); ?>
   </button>
   <?php } else { ?>
   <button class="cursor-pointer" type="button" x-on:click.prevent="$store.login.method='intro';login.showModal()">
      Inscrições
   </button>
   <?php } ?>
</div>
