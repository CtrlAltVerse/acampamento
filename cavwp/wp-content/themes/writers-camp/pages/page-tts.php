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
   $intro = [
      'name' => 'Intro',
      'src' => wp_get_attachment_url($term->get('intro'))
   ];
   $outro = [
      'name' => 'Outro',
      'src' => wp_get_attachment_url($term->get('outro'))
   ];
}

// header
$header = "<p>&amp;quot;{$title}&amp;quot;, de {$author}</p><p>Publicado em &amp;quot;{$club}&amp;quot; no {$site}.</p>";

// middle
$content = Utils::json_to_ssml($Text->get('raw_json'));

// footer
$footer = "<p>Este foi &amp;quot;{$title}&amp;quot;, de {$author}</p><p>Publicado no {$site}.</p><p>Deixe seu comentário em <lang xml:lang='en-US'>alt vers</lang> ponto <lang xml:lang='en-US'>net</lang> barra <say-as interpret-as='verbatim'>{$link}</say-as>.</p>";

$requests = [''];

foreach ($content as $paragraph) {
   $key = count($requests) - 1;

   if (strlen($requests[$key]) + strlen($paragraph) >= 5000) {
      $requests[] = stripslashes($paragraph);
   } else {
      $requests[$key] .= stripslashes($paragraph);
   }
}

$requests = array_merge([$header, $intro ?? ''], $requests, [$outro ?? '', $footer]);

get_component('header');

?>
<main class="container my-6" x-data="tts">
   <div class="flex items-center justify-between mb-4">
      <h1 class="text-3xl font-semibold">Criar áudio: <?php echo $title ?></h1>
   </div>
   <div class="flex flex-col lg:flex-row items-start gap-8">
      <form id="global" class="w-full flex flex-col items-start gap-3">
         <h2 class="font-bold">Opções</h2>
         <div class="flex gap-4">
            <h3 class="font-medium">
               Velocidade
            </h3>
            <input name="rate" type="number" value="1.025" step="0.025" min="0.95" max="1.3" />
         </div>
         <div class="flex gap-4">
            <h3 class="font-medium">Voz</h3>
            <select class="dark:bg-neutral-700 dark:text-neutral-100" x-model="genre">
               <option value="M">Masculinas</option>
               <option value="F">Femininas</option>
            </select>
         </div>
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
                        <source x-bind:src="voice.file" type="audio/wav" />
                     </audio>
                  </template>
               </li>
            </template>
         </ul>
         <input name="title" type="hidden" value="<?php echo $slug; ?>" />
      </form>
      <div class="relative max-w-2xl w-full mx-auto ml-3.5 md:ml-0">
         <?php foreach ($requests as $number => $request) { ?>
            <?php if (is_array($request)) { ?>
               <div class="flex items-center gap-4 mb-12">
                  <strong>Vinheta: <?php echo $request['name'] ?></strong>
                  <audio controls>
                     <source src="<?php echo $request['src']; ?>" type="audio/ogg" />
                  </audio>
               </div>
            <?php } else { ?>
               <form class="mb-12" x-on:submit.prevent="requestAudio">
                  <div class="flex items-center gap-4 mb-2">
                     <button class="btn" type="submit">Criar áudio</button>
                     <strong><?php echo strlen($request); ?> caracteres</strong>
                     <audio class="hidden" controls>
                     </audio>
                  </div>
                  <textarea name="text" class="w-full" rows="5"
                     readonly><speak><?php echo str_replace('"', '\"', $request); ?></speak></textarea>
                  <input name="number" type="hidden"
                     value="<?php echo $number; ?>" />
               </form>
            <?php } ?>
         <?php } ?>
      </div>
   </div>
</main>
<?php

get_component('footer');

?>
