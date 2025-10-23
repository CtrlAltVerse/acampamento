<?php

use cavEx\Shortlink\Utils as ShortlinkUtils;
use cavWP\Models\Post;
use writersCampP\Challenge\Utils as ChallengeUtils;

get_component('header');

the_post();

$from_challenge = [];
$series_itens   = [];

$Text      = new Post();
$full      = $Text->get('image_full');
$challenge = $Text->get('challenge');
$shares    = $Text->get('share', attrs: ['facebook', 'x-twitter', 'email', 'whatsapp']);
$terms     = $Text->get('terms', taxonomy: 'club');

if (!empty($terms)) {
   $term = $terms[0];
}
$series = $Text->get('terms', taxonomy: 'series');

if (!empty($series)) {
   $serie = $series[0];

   $series_itens = $serie->get_posts([
      'posts_per_page' => -1,
      'orderby'        => ['menu_order' => 'ASC', 'date' => 'desc'],
      'post_status'    => is_preview() ? ['publish', 'pending', 'draft', 'future'] : ['publish', 'future'],
   ]);

   if (!empty($series_itens)) {
      $current = $Text->get('menu_order');

      $min_position = 1;
      $max_position = $current + 2;

      if ($current <= 2) {
         $max_position = 4;
      } else {
         $min_position = $current - 1;
      }

      if ($current > count($series_itens) - 3) {
         $min_position = count($series_itens) - 3;
         $max_position = count($series_itens);
      }
   }
}

if (!empty($challenge)) {
   $from_challenge = ChallengeUtils::get_texts($challenge);
}

$from_club   = $Text->related(4, 'term', 'club', exclude: array_merge($from_challenge, $series_itens));
$from_author = $Text->related(4, 'author', exclude: array_merge($from_club, $from_challenge, $series_itens));

$reading_time = $Text->get('time_to_read');
$color        = $Text->get('color', default: false);

$container_class = '';

?>
<?php if (!empty($full)) { ?>
<?php $container_class = 'absolute top-[100vh] w-full overflow-safe'; ?>
<div class="fullscreen-image absolute top-safe left-0 justify-start items-end h-safe overflow-safe" aria-hidden="true">
   <img class="absolute inset-0 z-0 size-full object-cover"
        src="<?php echo $full; ?>" loading="eager" alt="">
   <div class="container h-full">
      <div
           class="title-effect z-1 <?php echo $color ? 'text-neutral-900 text-shadow-neutral-100' : 'text-neutral-100 text-shadow-neutral-900'; ?>">
         <?php if (!empty($term)) { ?>
         <span
               class="rounded py-1 px-2 uppercase font-extrabold text-sm text-shadow-none
         <?php echo $color ? 'text-neutral-900 bg-neutral-100' : 'text-neutral-100 bg-neutral-900'; ?>">
            <?php echo $term->get('name'); ?>
         </span>
         <?php } ?>
         <div class="h1">
            <?php echo $Text->get('title'); ?>
         </div>
         <p class="flex items-center gap-1 text-lg font-medium">
            <?php echo $Text->get('author:avatar', size: 32, attrs: ['class' => 'rounded-full']); ?>
            <?php echo $Text->get('author:display_name'); ?>
         </p>
         <div class="text-xl font-medium w-full pt-4">
            <?php echo $Text->get('summary'); ?>
         </div>
         <div class="flex items-center gap-1 pt-4">
            Foto de
            <a class="font-semibold"
               href="<?php echo $Text->get('image_author_url'); ?>"
               target="_blank"
               rel="external"><?php echo $Text->get('image_author'); ?></a>
            no <a class="font-semibold"
               href="https://unsplash.com/?utm_source=CtrlAltVersœ&utm_medium=referral"
               target="_blank" rel="external">Unsplash</a>
            </a>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<div class="<?php echo $container_class; ?>">
   <main class="main !mt-25" x-data="{comment:$persist(''),parent: 0, reply_to: ''}">
      <article class="grid grid-cols-1 xl:grid-cols-main gap-9">
         <div class="flex flex-col justify-between gap-3">
            <div>
               <div class="title-effect" x-ref="singleTitle" x-cloak>
                  <?php if (!empty($term)) { ?>
                  <a href="<?php echo $term->get('link'); ?>"
                     class="rounded py-1 px-2 uppercase font-extrabold text-sm text-neutral-100"
                     style="background-color: <?php echo $term->get('color'); ?>">
                     <?php echo $term->get('name'); ?>
                  </a>
                  <?php } ?>
                  <h1 class="h1">
                     <?php echo $Text->get('title'); ?>
                  </h1>
                  <a href="<?php echo $Text->get('author:link'); ?>"
                     class="flex items-center gap-1 text-lg font-medium">
                     <?php echo $Text->get('author:avatar', size: 32, attrs: ['class' => 'rounded-full']); ?>
                     <?php echo $Text->get('author:display_name'); ?>
                  </a>
                  <div class="text-xl font-medium w-full pt-4">
                     <?php echo $Text->get('summary'); ?>
                  </div>
                  <div class="flex gap-6">
                     <span><?php echo $Text->get('date'); ?></span>
                     <span>
                        <?php printf(_n('%d minuto de leitura', '%d minutos de leitura', $reading_time, 'cav'), $reading_time); ?>
                     </span>
                  </div>
                  <ul class="flex flex-wrap items-center gap-3" x-data="{show: false}">
                     <li x-show="!show"><button class="cursor-pointer" type="button"
                                x-on:click.prevent="show=true">Compartilhar</button></li>
                     <li x-show="show" x-cloak>
                        <ul class="flex gap-3">
                           <li class="text-xl">
                              <button class="cursor-pointer" x-on:click.prevent="navigator.share({
                                    title: '<?php echo $Text->get('title'); ?>',
                                    text: '<?php echo $Text->get('summary', apply_filter: false); ?>',
                                    url: '<?php echo $Text->get('url'); ?>',
                                 })" type="button" title="Compartilhar">
                                 <i class="ri-share-fill"></i>
                              </button>
                           </li>

                           <?php $share = $Text->get('share_image'); ?>
                           <?php if (!empty($share)) { ?>
                           <li class="text-xl">
                              <a class="cursor-pointer"
                                 href="<?php echo $share; ?>"
                                 title="Baixar imagem"
                                 download="share-<?php echo $Text->ID; ?>.png"
                                 target="_blank">
                                 <i class="ri-instagram-line"></i>
                              </a>
                           </li>
                           <?php } ?>

                           <?php $share_link = $Text->get('share_link_image'); ?>
                           <?php if (!empty($share_link)) { ?>
                           <li class="text-xl">
                              <a class="cursor-pointer"
                                 href="<?php echo $share_link; ?>"
                                 title="Baixar imagem com link"
                                 download="share-link-<?php echo $Text->ID; ?>.png"
                                 target="_blank">
                                 <i class="ri-qr-code-line"></i>
                              </a>
                           </li>
                           <?php } ?>

                           <?php foreach ($shares as $share) { ?>
                           <li class="text-xl" x-show="show">
                              <a href="<?php echo $share['share']; ?>"
                                 target="_blank"
                                 title="Compartilhar no <?php echo $share['name']; ?>">
                                 <i
                                    class="<?php echo $share['icon']; ?>"></i>
                              </a>
                           </li>
                           <?php } ?>

                           <?php $link      = $Text->get('link'); ?>
                           <?php $shortlink = ShortlinkUtils::get_link($Text->get('shortlink')); ?>
                           <?php if (!empty($shortlink)) {
                              $link = $shortlink['link'];
                           } ?>

                           <li class="relative text-xl sharing" x-show="show">
                              <button class="cursor-pointer" type="button" title="Copiar link"
                                      x-on:click.prevent="copy('<?php echo $link; ?>')">
                                 <i class="ri-link"></i>
                              </button>
                           </li>
                        </ul>
                     </li>
                     <li><a href="#comments" x-on:click.prevent="$do('scroll','#comments')">Comentar</a></li>
                     <li><a href="#related" x-on:click.prevent="$do('scroll','#related')">Relacionados</a></li>
                     <?php if (current_user_can('edit_others_posts')) { ?>
                     <?php edit_post_link(); ?>
                     <?php } ?>
                  </ul>
                  <p class="mt-5 font-xs" x-show="bookmark === null" x-cloak>
                     <i class="ri-bookmark-3-fill"></i>
                     Clique num paragrafo para adicionar uma marcação
                  </p>
               </div>
            </div>
            <div class="mb-6 hidden xl:block">
               <?php get_page_component(__FILE__, 'comment-form', ['in_body' => true, 'title' => sprintf('O que achou de %s?', $Text->get('title', apply_filter: false))]); ?>
            </div>
         </div>
         <div class="pt-13">
            <div id="content" class="content mb-22">
               <?php echo $Text->get('content'); ?>
            </div>
            <div x-show="bookmark !== null && bookmark.url.length && bookmark.url === currentUrl" x-cloak>
               <button class="border rounded py-2 px-3 text-md cursor-pointer" x-on:click.prevent="cleanBookmark()"
                       type="button">
                  <i class="ri-bookmark-2-line"></i>
                  Remover marca-página
               </button>
            </div>
            <div class="xl:hidden block mt-6">
               <?php get_page_component(__FILE__, 'comment-form', ['in_body' => true, 'title' => sprintf('O que achou de %s?', $Text->get('title', apply_filter: false))]); ?>
            </div>
            <footer class="flex flex-col items-start gap-3 pt-20 w-full max-w-xl">
               <a
                  href="<?php echo $Text->get('author:link'); ?>">
                  <?php echo $Text->get('author:avatar', attrs: [
                     'class' => 'rounded-full',
                  ]); ?>
               </a>
               <h2 class="font-semibold text-xl">
                  <a
                     href="<?php echo $Text->get('author:link'); ?>">
                     Sobre
                     <?php echo $Text->get('author:name'); ?>
                  </a>
               </h2>
               <?php echo $Text->get('author:description'); ?>
               <ul class="flex gap-4 text-xl">
                  <?php $site_url = $Text->get('author:user_url'); ?>
                  <?php if (!empty($site_url)) { ?>
                  <li>
                     <a href="<?php echo $site_url; ?>"
                        target="_blank" rel="external">
                        <i class="ri-global-line"></i>
                     </a>
                  </li>
                  <?php } ?>
                  <?php $socials = $Text->get('author:socials'); ?>
                  <?php if (!empty($socials)) { ?>
                  <?php foreach ($socials as $social) { ?>
                  <li>
                     <a href="<?php echo $social['profile']; ?>"
                        target="_blank" rel="external">
                        <i
                           class="<?php echo $social['icon']; ?>"></i>
                     </a>
                  </li>
                  <?php } ?>
                  <?php } ?>
               </ul>
            </footer>
         </div>
      </article>
      <div id="related" class="flex flex-col gap-12 mt-15" x-init="checkTitle" x-on:scroll.window.passive="checkTitle"
           x-on:resize.window.passive="checkTitle">
         <?php if (!empty($series_itens)) { ?>
         <section class="flex flex-col gap-4">
            <h2 class="h2">
               <a
                  href="<?php echo $serie->get('link'); ?>">
                  <?php echo $serie->get('title'); ?>
                  <em class="text-md">(<?php echo count($series_itens); ?>
                     partes)</em>
               </a>
            </h2>
            <ul class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
               <?php foreach ($series_itens as $serie_item) { ?>
               <?php $position = $serie_item->menu_order; ?>
               <?php if ($min_position <= $position && $position <= $max_position) {
                  ?>
               <li>
                  <?php get_component('feature', ['text' => $serie_item, 'small' => true]); ?>
               </li>
               <?php } ?>
               <?php } ?>
            </ul>
         </section>
         <?php } ?>

         <?php if (!empty($challenge)) { ?>
         <section class="flex flex-col gap-4">
            <h2 class="h2">Parte do Desafio</h2>
            <?php get_component('challenge', ['challenge' => new Post($challenge)]); ?>
         </section>
         <?php } ?>

         <?php if (!empty($from_club)) { ?>
         <section class="flex flex-col gap-4">
            <h2 class="h2">
               Mais em
               <?php echo $term->get('name', apply_filter: false); ?>
            </h2>
            <ul class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
               <?php foreach ($from_club as $from_club_item) { ?>
               <li>
                  <?php get_component('feature', ['text' => $from_club_item, 'small' => true]); ?>
               </li>
               <?php } ?>
            </ul>
         </section>
         <?php } ?>

         <?php if (!empty($from_author)) { ?>
         <section class="flex flex-col gap-4">
            <h2 class="h2">
               Mais de
               <?php echo $Text->get('author:display_name'); ?>
            </h2>
            <ul class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
               <?php foreach ($from_author as $from_author_item) { ?>
               <li>
                  <?php get_component('feature', ['text' => $from_author_item, 'small' => true]); ?>
               </li>
               <?php } ?>
            </ul>
         </section>
         <?php } ?>
      </div>
      <section id="comments" class="flex flex-col gap-4 mt-15" x-init="">
         <h2 class="h2">Comentários</h2>
         <div class="grid grid-cols-1 xl:grid-cols-main gap-9">
            <div>
               <?php get_page_component(__FILE__, 'comment-form', ['title' => esc_html__('Deixar um comentário', 'cavwp')]); ?>
            </div>
            <div>
               <?php get_page_component(__FILE__, 'comments'); ?>
            </div>
         </div>
      </section>
   </main>
   <?php get_component('footer'); ?>
</div>
