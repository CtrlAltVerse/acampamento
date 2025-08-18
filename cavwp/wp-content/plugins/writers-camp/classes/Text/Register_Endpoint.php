<?php

namespace writersCampP\Text;

use WP_REST_Response;
use WP_REST_Server;

class Register_Endpoint
{
   public function __construct()
   {
      add_action('rest_api_init', [$this, '_create_endpoints']);
   }

   public function _create_endpoints(): void
   {
      register_rest_route('wrs-camp/v1', '/comment', [
         'methods'             => WP_REST_Server::CREATABLE,
         'callback'            => [$this, 'create_comment'],
         'permission_callback' => ['writersCampP\Utils', 'checks_login'],
         'parse_errors'        => true,
         'args'                => Utils::get_comment_fields(),
      ]);

      register_rest_route('wrs-camp/v1', '/text', [
         'methods'             => WP_REST_Server::CREATABLE,
         'callback'            => [$this, 'create_text'],
         'permission_callback' => ['writersCampP\Utils', 'checks_login'],
         'parse_errors'        => true,
         'args'                => Utils::get_text_fields(),
      ]);
   }

   public function create_comment($request)
   {
      $body = $request->get_params();

      $comment = wp_handle_comment_submission($body);

      if (is_wp_error($comment)) {
         return $comment;
      }

      $html = wp_list_comments([
         'walker'    => new Walker_Comment(),
         'max_depth' => 1,
         'echo'      => false,
      ], [$comment]);

      if (empty($body['comment_parent'])) {
         $actions[] = [
            'action'  => 'prepend',
            'target'  => '#comments-list',
            'content' => $html,
         ];
      } else {
         $comment_children = get_comments([
            'count'  => true,
            'parent' => $body['comment_parent'],
         ]);

         if (1 === $comment_children) {
            $actions[] = [
               'action'  => 'after',
               'target'  => "#comment-{$body['comment_parent']} > article",
               'content' => "<ul class=\"children\">{$html}</ul>",
            ];
         } else {
            $actions[] = [
               'action'  => 'prepend',
               'target'  => "#comment-{$body['comment_parent']} > ul",
               'content' => $html,
            ];
         }
      }

      return new WP_REST_Response($actions);
   }

   public function create_text($request)
   {
      $raw = $request->get_params();

      if (!empty($raw['ID'])) {
         $body['ID'] = $raw['ID'];
      }

      $body['post_type']    = 'text';
      $body['post_author']  = get_current_user_id();
      $body['post_title']   = $raw['post_title'];
      $body['post_content'] = $raw['post_content'];
      $body['post_excerpt'] = $raw['post_excerpt'];

      if ('save' === $raw['mode']) {
         $body['post_status'] = 'pending';
      }

      foreach (['challenge', 'color', 'image_author', 'image_author_url', 'image_full', 'image_mini'] as $meta) {
         if (!empty($raw[$meta])) {
            $body['meta_input'][$meta] = $raw[$meta];
         }
      }

      $body['tax_input']['club'] = [$raw['club']];

      $post_ID = wp_insert_post($body, true);

      if (is_wp_error($post_ID)) {
         return $post_ID;
      }

      if (!empty($raw['challenge'])) {
         $challenge = (int) $raw['challenge'];
         $texts     = get_post_meta($challenge, 'texts', true);

         if (empty($texts)) {
            $texts = [];
         }

         if (empty($raw['slot'])) {
            $texts[] = $post_ID;
         } else {
            $texts[$raw['slot']] = $post_ID;
         }

         update_post_meta($challenge, 'texts', $texts);
      }

      $actions[] = [
         'action'  => 'value',
         'target'  => '#ID',
         'content' => $post_ID,
      ];

      if ('save' === $raw['mode']) {
         $actions[] = [
            'action'  => 'toast',
            'content' => 'Texto enviado para revisão!',
         ];
      } else {
         $actions[] = [
            'action'  => 'toast',
            'content' => 'Rascunho salvo',
         ];
      }

      return new WP_REST_Response($actions);
   }
}
