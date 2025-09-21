<?php

use cavWP\Form;
use cavWP\Models\Post;
use writersCampP\Text\Utils as TextUtils;
use writersCampP\Utils;

$Form = new Form(TextUtils::get_draft_fields());

$text = $_GET['edit'] ?? null;

if (!empty($text)) {
   if (!current_user_can('edit_post', $text)) {
      wp_die('Você não tem permissão para editar esta publicação.');
   }

   $Text  = new Post($text);
   $clubs = $Text->get('terms', taxonomy: 'club');

   if (!empty($clubs)) {
      $club = $clubs[0]->ID;
   }
}

$challenge = $_GET['desafio'] ?? ($text ? $Text->get('challenge') : '');

get_component('header');

?>
<main class="container my-6" x-data="typewriter">
   <form id="editorForm" x-on:submit.prevent="save()">
      <div class="flex items-center justify-between mb-4">
         <h1 class="text-3xl font-semibold">Editando</h1>
         <div>
            <a class="btn" href="<?php echo Utils::get_page_link('dashboard'); ?>">
               <i class="ri-arrow-left-line"></i>
               <span class="hidden sm:inline">Voltar</span>
            </a>
            <button class="btn" type="button" x-on:click.prevent="save('draft')">
               <i class="ri-save-3-fill"></i>
               <span class="hidden sm:inline">Salvar</span>
            </button>
            <button class="btn" type="submit">
               <i class="ri-file-check-fill"></i>
               <span class="hidden sm:inline">Enviar</span>
            </button>
         </div>
      </div>
      <div class="flex flex-col lg:flex-row items-start gap-8">
         <div class="grow lg:sticky top-6 w-full flex flex-col items-start gap-3">
            <?php $Form->field('post_title', [
               'class'                      => 'h1 w-full input',
               'value'                      => $text ? $Text->get('title', apply_filter: false) : '',
               'x-model.fill'               => 'entry.title',
               'x-on:keydown.enter.prevent' => true,
               'x-autosize'                 => true,
            ], 'textarea'); ?>
            <?php $Form->field('post_excerpt', [
               'class'                      => 'h2 w-full input',
               'value'                      => $text ? $Text->get('summary', apply_filter: false) : '',
               'x-model.fill'               => 'entry.summary',
               'x-on:keydown.enter.prevent' => true,
               'x-autosize'                 => true,
            ], 'textarea'); ?>
            <div class="flex gap-3">
               <div class="flex flex-col gap-1 rounded py-2 px-3 focus-within:bg-neutral-100/30">
                  <?php $Form->label('club', [
                     'class' => 'font-semibold',
                  ]); ?>
                  <?php $Form->field('club', [
                     'class' => 'dark:bg-neutral-700 dark:text-neutral-100',
                     'value' => $_GET['guilda'] ?? ($text ? $club : ''),
                  ], 'select'); ?>
               </div>
               <?php if ($challenge) { ?>
               <div class="flex flex-col gap-1 rounded py-2 px-3">
                  <?php $Form->label('challenge', [
                     'class' => 'font-semibold',
                  ]); ?>
                  <input type="text"
                         value="<?php echo get_the_title($challenge); ?>"
                         readonly />
                  <?php $Form->field('challenge', [
                     'value' => $challenge,
                     'type'  => 'hidden',
                  ], 'input'); ?>
               </div>
               <?php } ?>
            </div>
            <?php $Form->field('ID', [
               'value' => $_GET['edit'] ?? '',
               'type'  => 'hidden',
            ]); ?>
            <?php $Form->field('slot', [
               'value' => $_GET['slot'] ?? '',
               'type'  => 'hidden',
            ]); ?>
            <?php $Form->field('color', [
               'type'  => 'hidden',
               'value' => !empty($_GET['edit']) ? $Text->get('color') : '',
            ]); ?>
            <?php $Form->field('image_author', [
               'type'  => 'hidden',
               'value' => !empty($_GET['edit']) ? $Text->get('image_author') : '',
            ]); ?>
            <?php $Form->field('image_author_url', [
               'type'  => 'hidden',
               'value' => !empty($_GET['edit']) ? $Text->get('image_author_url') : '',
            ]); ?>
            <?php $Form->field('image_full', [
               'type'  => 'hidden',
               'value' => !empty($_GET['edit']) ? $Text->get('image_full') : '',
            ]); ?>
            <?php $Form->field('image_mini', [
               'type'         => 'hidden',
               'value'        => !empty($_GET['edit']) ? $Text->get('image_mini') : '',
               'x-model.fill' => 'entry.image_mini',
            ]); ?>
            <button class="flex justify-center items-center mx-2 border-2 border-middle rounded w-full max-w-120 aspect-card bg-neutral-200/80 dark:bg-neutral-900/80 cursor-pointer"
                    x-on:click.prevent="unsplash.showModal()"
                    x-bind:class="{'border-dashed': 0===entry.image_mini.length}"
                    type="button">
               <span x-show="0===entry.image_mini.length">Escolha uma imagem para ilustrar o texto</span>
               <template x-if="entry.image_mini.length!==0">
                  <img class="size-full object-cover" x-bind:src="entry.image_mini" alt="" loading="lazy" />
               </template>
            </button>
            <?php get_page_component(__FILE__, 'status'); ?>
         </div>
         <div id="editor" class="relative max-w-2xl w-full mx-auto ml-3.5 md:ml-0 "></div>
      </div>
   </form>
   <div class="hidden">
      <div class="menu-mark flex gap-px p-1 bg-neutral-100 text-neutral-700 rounded" x-show="!current.showChanger"
           x-transition>
         <template x-for="{name,label,icon} in sky.marks">
            <button class="btn-editor" type="button" x-on:click.prevent="mark(name)"
                    x-bind:class="{active: current.has.includes(name)}" x-bind:title="label">
               <i class="ri-fw" x-bind:class="icon"></i>
            </button>
         </template>
      </div>
      <div class="menu-block z-5 !-left-7 text-neutral-700 transition-all group"
           x-on:click.outside="current.showChanger=false">
         <button class="btn-editor opacity-0 group-hover:opacity-100 transition-opacity disabled:!opacity-0 -translate-x-px"
                 type="button"
                 title="Mover para cima (Ctrl+Alt+↑)"
                 x-on:click.prevent="move"
                 x-bind:disabled="current.position === 'first'">
            <i class="ri-fw ri-arrow-up-s-line"></i>
         </button>
         <div class="relative overflow-hidden z-5 rounded-l-4xl" x-bind:class="current.showChanger ? '!w-135' : ''"
              x-on:click.outside="current.showChanger=false">
            <button class="btn-editor cursor-ns-resize size-8 !rounded-full border-1 border-middle bg-neutral-100 text-neutral-700 opacity-50 group-hover:opacity-100 transition-opacity relative z-2"
                    type="button"
                    title="Trocar tipo de bloco"
                    x-on:click.prevent="current.showChanger=!current.showChanger"
                    x-bind:class="{'!opacity-100': current.showChanger }">
               <i x-bind:class="current.icon"></i>
            </button>
            <div class="menu-changer absolute top-1/2 -left-0 -translate-y-1/2 z-1 flex items-center gap-px py-1 pr-2 bg-neutral-100 rounded pl-10"
                 x-show="current.showChanger"
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 -translate-x-full"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition-all ease-in duration-99"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
               <template x-for="{name, label, icon, attr} in sky.align">
                  <button class="btn-editor" type="button" x-on:click.prevent="align(attr)"
                          x-show="!current.has.includes(attr) && current.alignable" x-bind:title="label">
                     <i class="ri-fw" x-bind:class="icon"></i>
                  </button>
               </template>
               <span class="text-neutral-300" x-show="current.alignable">|</span>
               <template x-for="{name, label, icon, attr} in sky.blocks">
                  <button class="btn-editor" type="button" x-on:click.prevent="node(name, attr)"
                          x-show="current.type !== name" x-bind:title="label">
                     <i class="ri-fw" x-bind:class="icon"></i>
                  </button>
               </template>
            </div>
         </div>
         <button class="btn-editor opacity-0 group-hover:opacity-100 transition-opacity disabled:!opacity-0 -translate-x-px"
                 type="button"
                 title="Mover para baixo (Ctrl+Alt+↓)"
                 x-on:click.prevent="move(false)"
                 x-bind:disabled="current.position === 'last'">
            <i class="ri-fw ri-arrow-down-s-line"></i>
         </button>
      </div>

   </div>
   <?php get_page_component(__FILE__, 'media-selector'); ?>
</main>
<?php

get_component('footer');

?>
