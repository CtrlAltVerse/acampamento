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
            if (get_comment_meta($entry['entity_ID'], 'redeemed', true) === '') {
               update_comment_meta($entry['entity_ID'], 'redeemed', 1);

               $comment   = get_comment($entry['entity_ID']);
               $recipient = (int) $comment->user_id;
               $points    = 1;
            }
            break;

         case 'post_updated':
            if ('text' === $entry['entity_details']['post_type'] && 'publish' === $entry['entity_details']['post_status'] && get_post_meta($entry['entity_ID'], 'redeemed', true) === '') {
               update_post_meta($entry['entity_ID'], 'redeemed', 1);

               $recipient = (int) $entry['entity_details']['post_author'];
               $points    = 10;

               if (!empty(get_post_meta($entry['entity_ID'], 'challenge', true))) {
                  $points += 15;
               }
            }
            break;

         default:
            break;
      }

      if (empty($recipient)) {
         return;
      }

      $current_xp = (int) get_user_meta($recipient, 'xp', true);
      update_user_meta($recipient, 'xp', $current_xp + $points);
   }
}
