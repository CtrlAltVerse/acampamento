<?php

namespace writersCampT;

class Register_Dashboard_New
{
   public function __construct()
   {
      add_action('wp_enqueue_scripts', [$this, 'enqueue_assets'], 9);
      add_action('wp_enqueue_scripts', [$this, 'localize_voices']);
   }

   public function enqueue_assets(): void
   {
      if (!is_page(['publish', 'profile', 'tts'])) {
         return;
      }

      wp_enqueue_script('dashboard', get_theme_file_uri('assets/publish.min.js'), [], false, [
         'strategy' => 'defer',
      ]);

      wp_localize_script('dashboard', 'sky', [
         'autosave'    => AUTOSAVE_INTERVAL,
         'unsplashUrl' => rest_url('unsplash/v1'),
         'marks'       => [
            [
               'name'     => 'bold',
               'label'    => 'Negrito',
               'shortcut' => 'Ctrl+B',
               'icon'     => 'ri-bold',
            ],
            [
               'name'     => 'italic',
               'label'    => 'Itálico',
               'shortcut' => 'Ctrl+I',
               'icon'     => 'ri-italic',
            ],
            [
               'name'     => 'underline',
               'label'    => 'Sublinhado',
               'shortcut' => 'Ctrl+U',
               'icon'     => 'ri-underline',
            ],
            [
               'name'     => 'strike',
               'label'    => 'Tachado',
               'shortcut' => 'Ctrl+Shift+S',
               'icon'     => 'ri-strikethrough',
            ],
            [
               'name'     => 'superscript',
               'label'    => 'Sobrescrito',
               'shortcut' => 'Ctrl+.',
               'icon'     => 'ri-superscript',
            ],
            [
               'name'     => 'subscript',
               'label'    => 'Subscrito',
               'shortcut' => 'Ctrl+,',
               'icon'     => 'ri-subscript',
            ],
            [
               'name'     => 'code',
               'label'    => 'Código',
               'shortcut' => 'Ctrl+E',
               'icon'     => 'ri-code-line',
            ],
         ],
         'align' => [
            [
               'name'     => 'textAlign',
               'label'    => 'Alinhar à esquerda',
               'shortcut' => 'Ctrl+Shift+L',
               'icon'     => 'ri-align-left',
               'attr'     => 'left',
            ],
            [
               'name'     => 'textAlign',
               'label'    => 'Centralizar',
               'shortcut' => 'Ctrl+Shift+E',
               'icon'     => 'ri-align-center',
               'attr'     => 'center',
            ],
            [
               'name'     => 'textAlign',
               'label'    => 'Alinhar à direita',
               'shortcut' => 'Ctrl+Shift+R',
               'icon'     => 'ri-align-right',
               'attr'     => 'right',
            ],
            [
               'name'     => 'textAlign',
               'label'    => 'Justificado',
               'shortcut' => 'Ctrl+Shift+J',
               'icon'     => 'ri-align-justify',
               'attr'     => 'justify',
            ],
         ],
         'blocks' => [
            [
               'name'        => 'paragraph',
               'label'       => 'Parágrafo',
               'icon'        => 'ri-paragraph',
               'placeholder' => 'Escreva um parágrafo',
            ],
            [
               'name'        => 'heading',
               'label'       => 'Título',
               'icon'        => 'ri-h-2',
               'placeholder' => 'Escreva um título',
               'attr'        => 2,
            ],
            // [
            //    'name'        => 'heading',
            //    'label'       => 'Subtítulo',
            //    'icon'        => 'ri-h-3',
            //    'placeholder' => 'Escreva um subtítulo',
            //    'attr'        => 3,
            // ],
            [
               'name'        => 'blockquote',
               'label'       => 'Citação',
               'shortcut'    => 'Ctrl+Shift+B',
               'icon'        => 'ri-double-quotes-l',
               'placeholder' => 'Escreva uma citação',
            ],
            [
               'name'        => 'bulletList',
               'label'       => 'Lista',
               'shortcut'    => 'Ctrl+Shift+8',
               'icon'        => 'ri-list-unordered',
               'placeholder' => 'Escreva uma lista',
            ],
            [
               'name'        => 'orderedList',
               'label'       => 'Lista numerada',
               'shortcut'    => 'Ctrl+Shift+7',
               'icon'        => 'ri-list-ordered-2',
               'placeholder' => 'Escreva uma lista numerada',
            ],
            [
               'name'        => 'codeBlock',
               'label'       => 'Bloco de código',
               'icon'        => 'ri-code-block',
               'placeholder' => 'Escreva código',
            ],
            [
               'name'  => 'horizontalRule',
               'label' => 'Linha horizontal',
               'icon'  => 'ri-separator',
            ],
         ],
      ]);
   }

   public function localize_voices()
   {
      if (!is_page('tts')) {
         return;
      }

      wp_localize_script(
         'dashboard',
         'allVoices',
         [
            ['name' => 'pt-BR-Chirp3-HD-Achernar', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Achird', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Algenib', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Algieba', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Alnilam', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Aoede', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Autonoe', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Callirrhoe', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Charon', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Despina', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Enceladus', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Erinome', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Fenrir', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Gacrux', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Iapetus', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Kore', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Laomedeia', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Leda', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Orus', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Puck', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Pulcherrima', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Rasalgethi', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Sadachbia', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Sadaltager', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Schedar', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Sulafat', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Umbriel', 'genre' => 'M'],
            ['name' => 'pt-BR-Chirp3-HD-Vindemiatrix', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Zephyr', 'genre' => 'F'],
            ['name' => 'pt-BR-Chirp3-HD-Zubenelgenubi', 'genre' => 'M'],
         ],
      );
   }
}
