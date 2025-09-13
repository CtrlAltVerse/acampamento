<?php

namespace writersCampP\Challenge;

use cavWP\Models\Post;

class Utils
{
   public static function get($count = 3, $only_names = false)
   {
      $query_args = [
         'post_type'      => 'challenge',
         'post_status'    => 'publish',
         'posts_per_page' => $count,
         'orderby'        => ['meta_value_num' => 'ASC', 'date' => 'ASC'],
         'meta_key'       => 'text_count',
         'meta_query'     => [[
            'key'     => 'text_count',
            'compare' => '<',
            'value'   => 4,
            'type'    => 'NUMERIC',
         ]],
      ];

      $challenges = get_posts($query_args);

      if ($only_names) {
         foreach ($challenges as $challenge) {
            $new_challenges[$challenge->ID] = $challenge->post_title;
         }

         return $new_challenges;
      }

      return array_map(fn($challenge) => new Post($challenge), $challenges);
   }

   public static function get_texts($challenge_ID)
   {
      $texts = [];
      $slots = get_field('slots', is_array($challenge_ID) ? $challenge_ID[0] : $challenge_ID);

      if (empty($slots)) {
         return $texts;
      }

      foreach ($slots as $slot) {
         if (empty($slot)) {
            continue;
         }

         $texts[] = $slot;
      }

      return $texts;
   }
}
