<?php

namespace writersCampP\Club;

use cavWP\Models\Term;

class Utils
{
   public static function get($only_ids = false)
   {
      $query_args = [
         'taxonomy'   => 'club',
         'parent'     => 0,
         'hide_empty' => false,
      ];

      if ($only_ids) {
         $query_args['fields'] = 'ids';
      }

      $categories = get_terms($query_args);

      if ($only_ids) {
         return $categories;
      }

      return array_map(fn($category) => new Term($category), $categories);
   }
}
