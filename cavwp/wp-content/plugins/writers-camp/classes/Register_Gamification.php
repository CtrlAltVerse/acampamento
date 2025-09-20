<?php

namespace writersCampP;

class Register_Gamification
{
   public function __construct()
   {
      add_action('cav_activity_log_added', [$this, 'trigger_points'], 10, 2);
   }

   public function trigger_points($type, $entry)
   {
      switch ($type) {
         case 'comment_approved':
            if (empty(get_comment_meta($entry['entity_ID'], 'redeemed', true))) {
               update_comment_meta($entry['entity_ID'], 'redeemed', 1);

               $comment   = get_comment($entry['entity_ID']);
               $recipient = (int) $comment->user_id;
               $points    = 1;
            }
            break;

         case 'post_updated':
            if ('text' !== $entry['entity_details']['post_type']) {
               return;
            }

            $recipient = (int) $entry['entity_details']['post_author'];

            if (
               in_array($entry['entity_details']['post_status'], ['publish', 'future']) && empty(get_post_meta($entry['entity_ID'], 'reviewer', true)) && (int) $entry['current_user_ID'] !== $recipient && !wp_doing_cron()
            ) {
               $current_xp = (int) get_user_meta($entry['current_user_ID'], 'xp', true);
               update_user_meta($entry['current_user_ID'], 'xp', $current_xp + 5);
               update_post_meta($entry['entity_ID'], 'reviewer', $entry['current_user_ID']);
            }

            if (
               'publish' === $entry['entity_details']['post_status'] && empty(get_post_meta($entry['entity_ID'], 'redeemed', true))
            ) {
               update_post_meta($entry['entity_ID'], 'redeemed', 1);

               $points = 10;

               if (!empty(get_post_meta($entry['entity_ID'], 'challenge', true))) {
                  $points += 15;
               }
            }
            break;

         default:
            break;
      }

      if (empty($recipient) || empty($points)) {
         return;
      }

      $current_xp = (int) get_user_meta($recipient, 'xp', true);
      update_user_meta($recipient, 'xp', $current_xp + $points);
   }
}
