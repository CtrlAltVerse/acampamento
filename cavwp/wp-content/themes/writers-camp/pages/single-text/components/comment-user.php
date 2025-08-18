<?php

use writersCampP\Utils;
use cavWP\Models\User;
use cavWP\Models\Post;

$User  = new User(get_current_user_id());
$Text  = new Post();

?>
<?php if (is_user_logged_in()) { ?>
   <?php echo $User->get('avatar', size: 32, attrs: ['class' => 'rounded']); ?>
   <span class="font-medium">
      <?php echo $User->get('name'); ?>
   </span>
   <a href="<?php echo Utils::get_page_link('profile'); ?>"
      target="_blank">Atualizar perfil</a>
   <a
      href="<?php echo wp_logout_url($Text->get('link')); ?>">Sair</a>
<?php } else { ?>
   <button class="cursor-pointer" type="button" x-on:click.prevent="$store.login.method='login';login.showModal()">Entrar com e-mail</button>
   <button class="cursor-pointer" type="button" x-on:click.prevent="$store.login.method='intro';login.showModal()">Facebook</button>
   <button class="cursor-pointer" type="button" x-on:click.prevent="$store.login.method='intro';login.showModal()">Google</button>
<?php } ?>
