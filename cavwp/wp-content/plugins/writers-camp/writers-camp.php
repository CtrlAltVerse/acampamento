<?php

namespace writersCampP;

/*
 * Plugin Name: Acampamento de Escritores
 * Plugin URI: https://acampamento.altvers.net
 * Description: Recursos essenciais para o site Acampamento de Escritores.
 * Version: 1.0
 * Author: CtrlAltVersœ
 * Author URI: https://ctrl.altvers.net/
 */

define('WRITERS_CAMP_FILE', __FILE__);

add_action('plugins_loaded', 'writersCampP\plugins_loaded');
function plugins_loaded(): void
{
   if (!function_exists('\cav_autoloader')) {
      return;
   }

   $AutoLoader = \cav_autoloader();
   $AutoLoader->add_namespace('writersCampP', implode(DIRECTORY_SEPARATOR, [__DIR__, 'classes']));

   include_once 'classes/Register_Fields.php';

   new Register();
   new Club\Register();
   new Text\Register();
   new Media\Register_Endpoint();
   new Writer\Register();
   new Challenge\Register();
}
