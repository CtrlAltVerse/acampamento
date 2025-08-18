<?php

namespace writersCampP;

class Register_Shortcodes
{
   public function __construct()
   {
      add_shortcode('ranks', [$this, 'ranks_cb']);
   }

   public function ranks_cb()
   {
      $output = '<div class="flex flex-wrap gap-10 my-8">';
      $ranks  = array_reverse(array_keys(Utils::get_rank()));

      foreach ($ranks as $rank) {
         $rank = Utils::get_rank($rank);

         ob_start();
         get_component('level', ['rank' => $rank]);

         $output .= ob_get_clean();
      }

      return $output . '</div>';
   }
}
