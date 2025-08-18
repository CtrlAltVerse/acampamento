<?php

namespace writersCampT;

add_action('wp_loaded', 'writersCampT\theme_loaded');
function theme_loaded(): void
{
   if (!function_exists('\cav_autoloader')) {
      return;
   }

   $AutoLoader = \cav_autoloader();
   $AutoLoader->add_namespace('writersCampT', implode(DIRECTORY_SEPARATOR, [__DIR__, 'classes']));

   new Register();
}
