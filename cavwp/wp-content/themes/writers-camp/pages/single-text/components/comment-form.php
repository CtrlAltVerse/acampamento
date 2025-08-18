<?php

use cavWP\Form;
use cavWP\Models\Post;
use cavWP\Models\User;
use writersCampP\Text\Utils as TextUtils;


$Text  = new Post();
$User  = new User(get_current_user_id());
$Form  = new Form(TextUtils::get_comment_fields());
$title = $args['title'];

$container_class = 'sticky top-15';

if ($args['in_body'] ?? false) {
   $container_class = '';
}

?>
<div id="respond"
     class="flex flex-col gap-3 <?php echo $container_class; ?>">
   <h3 class="h3"
       x-text="parent === 0 ? '<?php echo $title; ?>' : reply_to">
      <?php echo $title; ?>
   </h3>
   <form class="flex flex-col gap-3"
         x-on:submit.prevent="$rest.post(moon.apiUrl+'/comment?_wpnonce='+moon.nonce).then(() => {$do('value','[name=comment]','');parent=0})">
      <div class="comment-user flex items-center gap-3">
         <?php get_page_component(__FILE__,'comment-user'); ?>
      </div>
      <div>
         <?php $Form->field('comment', [
            'class'       => 'w-full input',
            'rows'        => '2',
            'aria-label'  => 'Comentário',
            'placeholder' => 'Adicionar comentário',
            'x-model'       => 'comment',
            'x-autosize'  => true,
         ], 'textarea'); ?>
      </div>
      <div class="flex items-center gap-12">
         <div>
            <button class="btn" type="submit">Publicar</button>
         </div>
         <div>
            <button class="cursor-pointer" type="button" x-show="parent>0" x-on:click.prevent="parent=0" x-cloak>
               Cancelar resposta
            </button>
         </div>
      </div>
      <?php $Form->field('comment_post_ID', [
         'type'  => 'hidden',
         'value' => get_the_ID(),
      ]); ?>
      <?php $Form->field('comment_parent', [
         'type'    => 'hidden',
         'x-model' => 'parent',
      ]); ?>
   </form>
</div>
