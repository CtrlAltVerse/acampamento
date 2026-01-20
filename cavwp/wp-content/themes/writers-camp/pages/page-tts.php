<?php

use cavEx\Shortlink\Utils as ShortlinkUtils;
use cavWP\Models\Post;
use writersCampP\Text\Utils;
use writersCampP\Utils as WritersCampPUtils;

$text = $_GET['edit'] ?? null;

if (empty($text)) {
   if (wp_safe_redirect(WritersCampPUtils::get_page_link('dashboard'))) {
      exit;
   }
}

if (!empty($text)) {
   if (!current_user_can('administrator', $text)) {
      wp_die('Você não tem permissão para editar esta publicação.');
   }

   $Text = new Post($text);
}

$title  = $Text->get('title');
$slug   = $Text->get('post_name');
$author = $Text->get('author:display_name');
$link   = $Text->get('link');

$shortlink = ShortlinkUtils::get_link($Text->get('shortlink'));

if (!empty($shortlink)) {
   $link = str_replace('https://altvers.net/', '', $shortlink['link']);
}

$site  = get_bloginfo('name');
$terms = $Text->get('terms', taxonomy: 'club');

$club     = '';
$vignette = '';

if (!empty($terms)) {
   $term  = $terms[0];
   $club  = $term->get('name', apply_filter: false);
   $intro = wp_get_attachment_url($term->get('intro'));
   $outro = wp_get_attachment_url($term->get('outro'));
}

// header
$header = "<p>&amp;quot;{$title}&amp;quot;, de {$author}</p><p>Publicado em &amp;quot;{$club}&amp;quot; no {$site}.</p>";

// middle
$content = Utils::json_to_ssml($Text->get('raw_json'));

// footer
$footer = "<p>Este foi &amp;quot;{$title}&amp;quot;, de {$author}</p><p>Publicado no {$site}.</p><p>Deixe seu comentário em <lang xml:lang=\"en-US\">alt vers</lang> ponto <lang xml:lang=\"en-US\">net</lang> barra <say-as interpret-as=\"verbatim\">{$link}</say-as>.</p>";

$requests = [''];

foreach ($content as $paragraph) {
   $key = count($requests) - 1;

   if (strlen($requests[$key]) + strlen($paragraph) >= 5000) {
      $requests[] = $paragraph;
   } else {
      $requests[$key] .= $paragraph;
   }
}

$requests = array_merge([$header], $requests, [$footer]);

get_component('header');

?>
<main class="container my-6" x-data="tts">
   <div class="flex items-center justify-between mb-4">
      <h1 class="text-3xl font-semibold">Criar áudio</h1>
   </div>
   <div class="flex flex-col lg:flex-row items-start gap-8">
      <form id="global" class="w-full flex flex-col items-start gap-3">
         <h2>Prévias das vozes</h2>
         <select class="dark:bg-neutral-700 dark:text-neutral-100" x-model="genre">
            <option value="M">Masculinas</option>
            <option value="F">Femininas</option>
         </select>
         <ul class="flex flex-col gap-1 text-lg font-mono w-full">
            <template x-for="voice in voices">
               <li class="flex items-center justify-between w-full"
                   x-show="voice.genre === genre">
                  <label>
                     <input type="radio" name="voice" x-bind:value="voice.id" />
                     <span x-text="voice.name"></span>
                  </label>
                  <template x-if="voice.file">
                     <audio controls>
                        <source x-bind:src="voice.file" type="audio/wav">
                     </audio>
                  </template>
               </li>
            </template>
         </ul>
         <input name="title" type="hidden"
                value="<?php echo $slug; ?>" />
      </form>
      <div class="relative max-w-2xl w-full mx-auto ml-3.5 md:ml-0">
         <?php foreach ($requests as $number => $request) { ?>
         <form class="mb-12" x-on:submit.prevent="requestAudio">
            <strong>N. de caracteres:
               <?php echo strlen($request); ?></strong>
            <button class="btn" type="submit">Criar áudio</button>
            <textarea name="text" class="w-full" rows="5"
                      readonly><speak><?php echo str_replace('"', '\"', $request); ?></speak></textarea>
            <input name="number" type="hidden"
                   value="<?php echo $number; ?>" />
         </form>
         <?php } ?>
      </div>
   </div>
</main>
<?php

get_component('footer');

?>
