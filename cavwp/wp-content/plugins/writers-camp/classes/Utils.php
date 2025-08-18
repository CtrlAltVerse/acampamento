<?php

namespace writersCampP;

use WP_Error;

class Utils
{
   public static function checks_login()
   {
      if (is_user_logged_in()) {
         return true;
      }

      return new WP_Error(403, 'Faça login primeiro.');
   }

   public static function get_page_link($key, $params = [])
   {
      $page_ID = \get_field("pages_{$key}", 'option');

      $params_url = '';

      if (!empty($params)) {
         $params_url = '?' . http_build_query($params);
      }

      return \esc_url(\get_permalink($page_ID) . $params_url);
   }

   public static function get_rank($level = null)
   {
      $ranks = [
         1000 => [
            'label' => 'Lenda',
            'color' => 'bg-linear-135 from-purple-500 to-orange-500 text-neutral-100',
         ],

         500 => [
            'label' => 'Grão-Mestre',
            'color' => 'bg-orange-500 text-neutral-100',
         ],

         250 => [
            'label' => 'Mestre',
            'color' => 'bg-purple-500 text-neutral-100',
         ],

         100 => [
            'label' => 'Veterano',
            'color' => 'bg-teal-500 text-neutral-700',
         ],

         50 => [
            'label' => 'Bardo',
            'color' => 'bg-yellow-400 text-neutral-700',
         ],

         25 => [
            'label' => 'Iniciante',
            'color' => 'bg-blue-400 text-neutral-100',
         ],

         10 => [
            'label' => 'Novato',
            'color' => 'bg-green-400 text-neutral-100',
         ],

         0 => [
            'label' => 'Lobinho',
            'color' => 'bg-neutral-500 text-neutral-100',
         ],
      ];

      if (is_null($level)) {
         return $ranks;
      }

      foreach ($ranks as $rank_min => $rank) {
         if ($rank_min <= $level) {
            return (object) [
               ...$rank,
               'level' => $level,
            ];
         }
      }
   }
}
