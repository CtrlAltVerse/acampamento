<?php

namespace writersCampT;

class Register
{
   public function __construct()
   {
      add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
      add_action('wp_resource_hints', [$this, 'add_resources'], 10, 2);
      add_action('cav_head_scripts', [$this, 'theme_changer']);

      new Register_Dashboard_New();
   }

   public function add_resources($urls, $type)
   {
      if ('preconnect' === $type) {
         $urls[] = [
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
         ];
      }

      return $urls;
   }

   public function enqueue_assets(): void
   {
      wp_enqueue_style('main', get_theme_file_uri('assets/main.min.css'));
      wp_enqueue_script('all', get_theme_file_uri('assets/all.min.js'), [], false, [
         'strategy' => 'defer',
      ]);

      wp_localize_script('all', 'moon', [
         'apiUrl' => rest_url('wrs-camp/v1'),
         'nonce'  => wp_create_nonce('wp_rest'),
      ]);
   }

   public function theme_changer()
   {
      echo <<<'HTML'
            <script>
            function changeTheme(){
               document.documentElement.classList.toggle( 'dark',
               localStorage.theme === 'dark' ||
               (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
            )}
            changeTheme()
            </script>
      HTML;
   }
}
