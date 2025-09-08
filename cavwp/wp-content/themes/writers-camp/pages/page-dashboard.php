<?php

use cavWP\Models\Post;
use cavWP\Models\User;
use cavWP\Utils;
use writersCampP\Utils as WritersCampPUtils;

$writer = new User(get_current_user_id());

$statuses = [
   'draft' => [
      'label' => 'Rascunhos',
      'color' => 'bg-yellow-300',
   ],
   'pending' => [
      'label' => 'Em revisão',
      'color' => 'bg-blue-300',
   ],
   'publish' => [
      'label' => 'Publicados',
      'color' => 'bg-green-300',
   ],
];
$statuses_keys = array_flip(array_keys($statuses));

$status = $_GET['status'] ?? 'draft';

if (!in_array($status, array_keys($statuses))) {
   $status = 'draft';
}

$all_texts = $writer->get('posts', attrs: [
   'post_type'      => 'text',
   'post_status'    => $status,
   'posts_per_page' => -1,
]);

usort($all_texts, function($a, $b) use ($statuses_keys) {
   $key_a = $statuses_keys[$a->post_status];
   $key_b = $statuses_keys[$b->post_status];

   return $key_a <=> $key_b;
});

$per_page = 25;
$page     = Utils::get_page();

foreach ($all_texts as $i => $raw_text) {
   if ($page * $per_page <= $i) {
      break;
   }

   if ($i < ($page - 1) * $per_page) {
      continue;
   }

   $texts[] = $raw_text;
}

$nav_items = Utils::paginate_links(
   [
      'total'     => ceil(count($all_texts) / $per_page),
      'mid_size'  => 3,
      'next_text' => 'Próxima <i class="ri-arrow-right-line"></i>',
      'prev_text' => '<i class="ri-arrow-left-line"></i> Anterior',
   ],
   [
      'page-numbers current' => 'btn active',
      'page-numbers'         => 'btn',
   ],
);

get_component('header');

?>
<div class="main">
   <main>
      <?php if ($writer->get('email_not_verified')) { ?>
      <div class="mx-auto w-full max-w-xl my-6 rounded py-2 px-4 bg-yellow-300 text-neutral-700" role="alert">
         <p>Para publicar textos e editar seu perfil, por favor, confirme seu endereço de e-mail.</p>
         <p>Se precisar de ajuda, <a
               href="<?php echo WritersCampPUtils::get_page_link('guide'); ?>#contato">entre
               em contato</a>.</p>
      </div>
      <?php } ?>
      <div class="flex items-center justify-between gap-2">
         <h1 class="h1">
            <?php esc_html_e('Suas publicações', 'cavcamp'); ?>
         </h1>
         <a class="btn"
            href="<?php echo WritersCampPUtils::get_page_link('edit'); ?>">
            <i class="ri-add-circle-fill"></i> <span class="hidden sm:inline">Novo texto</span>
         </a>
      </div>

      <ul class="flex gap-3 my-5 whitespace-nowrap overflow-x-auto !text-sm lg:!text-md">
         <?php foreach ($statuses as $status_k => $status_info) { ?>
         <li>
            <?php if ($status_k === $status) { ?>
            <span class="btn active">
               <?php echo $status_info['label']; ?>
            </span>
            <?php } else { ?>
            <a class="btn"
               href="<?php echo WritersCampPUtils::get_page_link('dashboard', ['status' => $status_k]); ?>">
               <?php echo $status_info['label']; ?>
            </a>
            <?php } ?>
         </li>
         <?php } ?>
      </ul>
      <div class="flex flex-col gap-6 lg:gap-3 divide-blue-100">
         <?php if (!empty($texts)) {
            foreach ($texts as $text) {
               $text        = new Post($text);
               $status      = $text->get('status');
               $count       = $text->get('comments_count');
               $status_info = $statuses[$status];

               ?>
         <div class="flex items-center gap-2.5">
            <div class="shrink-0">
               <?php $mini = $text->get('image_mini'); ?>
               <?php if ($mini) { ?>
               <img class="aspect-card rounded h-26 border-2 border-neutral-500 object-cover overflow-hidden"
                    src="<?php echo $mini; ?>" alt=""
                    loading="lazy" />
               <?php } else { ?>
               <div
                    class="flex items-center justify-center rounded aspect-card h-26 uppercase border-2 border-neutral-500 text-neutral-500 bg-neutral-200">
                  Sem imagem
               </div>
               <?php } ?>
               <ul class="flex justify-between lg:hidden mt-2">
                  <?php if ('publish' === $status) { ?>
                  <li>
                     <a class="btn small"
                        href="<?php echo $text->get('permalink'); ?>#comments"
                        title="<?php printf(
                           'Visualizar %s',
                           _n('comentário', 'comentários', $count, 'cavcamp'),
                        ); ?>">
                        <i class="ri-external-link-fill"></i>
                        Ver (<?php echo $count; ?>)
                     </a>
                  </li>
                  <li>
                     <button class="btn small"
                             type="button"
                             data-href="<?php echo WritersCampPUtils::get_page_link('edit', [
                                'draft' => $text->ID,
                             ]); ?>"
                             title="Tornar rascunho"
                             x-on:click.prevent="if(confirm('Para editar novamente este texto é preciso despublicá-lo e torná-lo um rascunho. Tem certeza?')){location.href=$event.target.dataset.href}">
                        <i class="ri-draft-line"></i>
                        Editar
                     </button>
                  </li>
                  <?php } else { ?>
                  <li>
                     <a class="btn small"
                        href="<?php echo WritersCampPUtils::get_page_link('edit', [
                           'edit' => $text->ID,
                        ]); ?>">
                        <i class="ri-edit-2-fill"></i>
                        Editar
                     </a>
                  </li>
                  <?php } ?>
               </ul>
            </div>
            <div class="grow flex flex-col gap-1 items-start">
               <strong
                       class="font-semibold text-lg line-clamp-2"><?php echo $text->get('title'); ?></strong>
               <p class="line-clamp-3">
                  <?php echo $text->get('summary', apply_filter: false); ?>
               </p>
            </div>
            <ul class="shrink-0 lg:flex flex-col gap-2.5 hidden">
               <?php if ('publish' === $status) { ?>
               <li>
                  <a class="btn small"
                     href="<?php echo $text->get('permalink'); ?>#comments"
                     title="<?php printf(
                        'Visualizar %s',
                        _n('comentário', 'comentários', $count, 'cavcamp'),
                     ); ?>">
                     <i class="ri-external-link-fill"></i>
                     Ver (<?php echo $count; ?>)
                  </a>
               </li>
               <li>
                  <button class="btn small"
                          type="button"
                          data-href="<?php echo WritersCampPUtils::get_page_link('edit', [
                             'draft' => $text->ID,
                          ]); ?>"
                          title="Tornar rascunho"
                          x-on:click.prevent="if(confirm('Para editar novamente este texto é preciso despublicá-lo e torná-lo um rascunho. Tem certeza?')){location.href=$event.target.dataset.href}">
                     <i class="ri-draft-line"></i>
                     Editar
                  </button>
               </li>
               <?php } else { ?>
               <li>
                  <a class="btn small"
                     href="<?php echo WritersCampPUtils::get_page_link('edit', [
                        'edit' => $text->ID,
                     ]); ?>">
                     <i class="ri-edit-2-fill"></i>
                     Editar
                  </a>
               </li>
               <?php } ?>
            </ul>
         </div>
         <?php } ?>
         <?php } else { ?>
         <div class="flex items-center justify-center min-h-60 italic">
            Não há publicações aqui.
         </div>
         <?php } ?>
      </div>
      <?php if (!empty($nav_items)) {    ?>
      <nav class="mt-4">
         <ul class="flex justify-center gap-2 *:flex">
            <?php foreach ($nav_items as $nav_item) { ?>
            <li>
               <?php echo $nav_item; ?>
            </li>
            <?php }    ?>
         </ul>
      </nav>
      <?php } ?>
   </main>
</div>
<?php

get_component('footer');

?>
