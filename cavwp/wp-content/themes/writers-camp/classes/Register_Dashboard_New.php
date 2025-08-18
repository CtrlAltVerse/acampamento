<?php

namespace writersCampT;

class Register_Dashboard_New
{
   public function __construct()
   {
      add_action('wp_enqueue_scripts', [$this, 'enqueue_assets'], 9);
   }

   public function enqueue_assets(): void
   {
      if (!is_page(['publish', 'profile'])) {
         return;
      }

      wp_enqueue_script('dashboard', get_theme_file_uri('assets/publish.min.js'), [], false, [
         'strategy' => 'defer',
      ]);

      wp_localize_script('dashboard', 'sky', [
         'unsplashUrl' => rest_url('unsplash/v1'),
         'colors'      => \get_field('colors', 'option'),
         'marks'       => [
            ['name' => 'bold', 'label' => 'Negrito (Ctrl+B)', 'icon' => 'ri-bold'],
            ['name' => 'italic', 'label' => 'Itálico (Ctrl+I)', 'icon' => 'ri-italic'],
            ['name' => 'underline', 'label' => 'Sublinhado (Ctrl+U)', 'icon' => 'ri-underline'],
            ['name' => 'strike', 'label' => 'Tachado (Ctrl+Shift+S)', 'icon' => 'ri-strikethrough'],
            ['name' => 'superscript', 'label' => 'Sobrescrito', 'icon' => 'ri-superscript'],
            ['name' => 'subscript', 'label' => 'Subscrito (Ctrl+,)', 'icon' => 'ri-subscript'],
            ['name' => 'code', 'label' => 'Código (Ctrl+E)', 'icon' => 'ri-code-line'],
         ],
         'align' => [
            [
               'name'  => 'textAlign',
               'label' => 'Alinhar à esquerda (Ctrl+Shift+L)',
               'icon'  => 'ri-align-left',
               'attr'  => 'left',
            ],
            [
               'name'  => 'textAlign',
               'label' => 'Centralizar (Ctrl+Shift+E)',
               'icon'  => 'ri-align-center',
               'attr'  => 'center',
            ],
            [
               'name'  => 'textAlign',
               'label' => 'Alinhar à direita (Ctrl+Shift+R)',
               'icon'  => 'ri-align-right',
               'attr'  => 'right',
            ],
            [
               'name'  => 'textAlign',
               'label' => 'Justificado (Ctrl+Shift+J)',
               'icon'  => 'ri-align-justify',
               'attr'  => 'justify',
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
               'label'       => 'Citação (Ctrl+Shift+B)',
               'icon'        => 'ri-double-quotes-l',
               'placeholder' => 'Escreva uma citação',
            ], [
               'name'        => 'bulletList',
               'label'       => 'Lista (Ctrl+Shift+8)',
               'icon'        => 'ri-list-unordered',
               'placeholder' => 'Escreva uma lista',
            ], [
               'name'        => 'orderedList',
               'label'       => 'Lista numerada (Ctrl+Shift+7)',
               'icon'        => 'ri-list-ordered-2',
               'placeholder' => 'Escreva uma lista numerada',
            ], [
               'name'  => 'horizontalRule',
               'label' => 'Linha horizontal',
               'icon'  => 'ri-more-fill',
            ], [
               'name'        => 'codeBlock',
               'label'       => 'Bloco de código',
               'icon'        => 'ri-code-block',
               'placeholder' => 'Escreva código',
            ],
         ],
      ]);
   }
}
