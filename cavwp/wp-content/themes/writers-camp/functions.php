<?php

namespace writersCampT;

add_action('wp_loaded', 'writersCampT\theme_loaded');
function theme_loaded(): void
{
   $AutoLoader = \cav_autoloader();
   $AutoLoader->add_namespace('writersCampT', implode(DIRECTORY_SEPARATOR, [__DIR__, 'classes']));

   new Register();
}
