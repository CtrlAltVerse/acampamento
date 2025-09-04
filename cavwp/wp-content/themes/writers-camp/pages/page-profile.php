<?php

use cavWP\Form;
use cavWP\Models\User;
use cavWP\Networks\Utils as NetworksUtils;
use writersCampP\Utils;
use writersCampP\Writer\Utils as WriterUtils;

$writer          = new User(get_current_user_id());
$rank            = Utils::get_rank($writer->get('xp', default: 0));
$fields          = WriterUtils::get_profile_fields();
$Form            = new Form($fields);
$Avatar          = new Form(WriterUtils::get_avatar_fields());
$all_socials     = NetworksUtils::get_services('profile');
$preview_socials = array_filter($fields, fn($field) => !empty($field['has_profile']));

get_component('header');

?>
<div
     x-data="<?php echo '{' . implode(',', array_map(fn($key) => "m_{$key}: ''", array_merge(array_keys($fields), ['avatar']))) . '}'; ?>">
   <div id="top" class="py-6 bg-brown-100 text-neutral-700">
      <div class="container flex flex-col lg:flex-row items-center justify-between gap-9 min-h-55">
         <div class="shrink-0 w-1/2 relative flex gap-9">
            <?php get_component('level', ['avatar' => 'm_avatar']); ?>
            <div class="flex flex-col justify-between pt-5 pb-11">
               <div class="flex flex-col gap-1">
                  <span class="text-2xl font-semibold" x-text="m_display_name">
                     <?php echo $writer->get('name'); ?>
                  </span>
                  <span
                        class="text-lg">@<?php echo $writer->get('slug'); ?></span>
               </div>
               <ul class="flex gap-4 flex-wrap mt-8 text-xl">
                  <li x-show="m_site_url" x-cloak>
                     <a x-bind:href="m_site_url" target="_blank" rel="external">
                        <i class="ri-global-line"></i>
                     </a>
                  </li>
                  <template
                            x-for="[key, value, link] in <?php echo '[' . implode(',', array_map(fn($key, $social) => "['{$key}', m_{$key}, '{$social['has_profile']}']", array_keys($preview_socials), $preview_socials)) . ']'; ?>">
                     <li x-show="value.length" x-cloak>
                        <a target="_blank" rel="external" x-bind:href="link.replace('%user%',value)">
                           <i x-bind:class="document.getElementById(key).dataset.icon"></i>
                        </a>
                     </li>
                  </template>
               </ul>
            </div>
         </div>
         <div class="grow" x-text="m_description">
            <?php echo nl2br($writer->get('description', default: '')); ?>
         </div>
      </div>
   </div>
   <main class="main flex gap-8">
      <div class="grow">
         <form class="flex flex-col gap-3 mb-9" x-data="avatar" x-on:submit.prevent="uploadAvatar">
            <div class="flex flex-col gap-1">
               <?php $Avatar->label('avatar', ['class' => 'font-medium']); ?>
               <?php $Avatar->field('avatar', [
                  'class'       => 'input',
                  'x-on:change' => 'm_avatar = $event.target.files[0]',
               ]); ?>
            </div>
            <div class="flex gap-3">
               <button class="btn" type="submit">Salvar</button>
               <button class="btn" type="button" x-on:click.prevent="$do('scroll','#top')">Prévia</button>
            </div>
         </form>

         <form class="grid grid-cols-1 lg:grid-cols-2 lg:flex-row gap-9"
               x-on:submit.prevent="$rest.put(moon.apiUrl+'/profile?_wpnonce='+moon.nonce)">
            <div class=" flex flex-col gap-3">
               <div class="flex flex-col gap-1">
                  <?php $Form->label('display_name', ['class' => 'font-medium']); ?>
                  <?php $Form->field('display_name', [
                     'class'        => 'input',
                     'value'        => $writer->get('name', apply_filter: false),
                     'x-model.fill' => 'm_display_name',
                  ]); ?>
               </div>
               <div class="flex flex-col gap-1">
                  <?php $Form->label('user_email', ['class' => 'font-medium']); ?>
                  <?php $Form->field('user_email', [
                     'class'        => 'input',
                     'value'        => $writer->get('email', apply_filter: false),
                     'x-model.fill' => 'm_user_email',
                  ]); ?>
               </div>
               <div class="flex flex-col gap-1">
                  <?php $Form->label('description', ['class' => 'font-medium'], 'textarea'); ?>
                  <?php $Form->field('description', [
                     'class'        => 'input',
                     'value'        => $writer->get('description', apply_filter: false),
                     'x-model.fill' => 'm_description',
                     'x-autosize'   => true,
                  ], 'textarea'); ?>
               </div>
               <div class="flex flex-col gap-1">
                  <?php $Form->label('site_url', ['class' => 'font-medium']); ?>
                  <?php $Form->field('site_url', [
                     'class'        => 'input',
                     'x-model.fill' => 'm_site_url',
                  ]); ?>
               </div>
               <div class="flex flex-col gap-3 mt-6">
                  <hgroup class=" flex flex-col gap-1">
                     <h3 class="h3">Trocar de senha</h3>
                     <p><em>Deixe em branco para manter a mesma.</em></p>
                  </hgroup>
                  <div class="flex flex-col gap-1">
                     <?php $Form->label('old_password', ['class' => 'font-medium']); ?>
                     <?php $Form->field('old_password', [
                        'class'        => 'input',
                        'autocomplete' => 'current-password',
                     ]); ?>
                  </div>
                  <div class="flex flex-col gap-1">
                     <?php $Form->label('user_password', ['class' => 'font-medium']); ?>
                     <?php $Form->field('user_password', [
                        'class'        => 'input',
                        'autocomplete' => 'new-password',
                     ]); ?>
                  </div>
               </div>

               <div class="flex gap-3 sticky top-1/2">
                  <button class="btn" type="submit">Salvar</button>
                  <button class="btn" type="button" x-on:click.prevent="$do('scroll','#top')">Prévia</button>
               </div>
            </div>
            <div class="flex flex-col gap-3">
               <em>Recomenda-se o preenchimento de até 3 redes mais usadas.</em>
               <?php foreach ($all_socials as $key => $social) { ?>
               <div class="flex flex-col gap-1">
                  <?php $Form->label($key, ['class' => 'font-medium'], p_attrs: false); ?>
                  <?php $Form->field($key, [
                     'class'        => 'input',
                     'value'        => $writer->get($key),
                     'data-icon'    => $social['icon'],
                     'x-model.fill' => "m_{$key}",
                  ]); ?>
               </div>
               <?php } ?>
               <div class="flex lg:hidden gap-3">
                  <button class="btn" type="submit">Salvar</button>
                  <button class="btn" type="button" x-on:click.prevent="$do('scroll','#top')">Prévia</button>
               </div>
            </div>
         </form>
      </div>
   </main>
</div>
<?php

get_component('footer');

?>
