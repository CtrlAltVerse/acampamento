<?php

namespace writersCampP\Text;

use writersCampP\Utils as WritersCampPUtils;

class Register
{
   public function __construct()
   {
      add_action('init', [$this, 'register']);
      add_action('wp_enqueue_scripts', [$this, 'set_post_content']);
      add_action('template_redirect', [$this, 'set_text_draft']);

      add_filter('comment_reply_link', [$this, 'filter_comment_reply_link']);
      add_filter('cav_head_metatags', [$this, 'set_metatags']);

      new Register_Endpoint();
   }

   public function filter_comment_reply_link($link)
   {
      return str_replace('class="comment-reply-link"', 'class="comment-reply-link" x-on:click.prevent="parent=$el.dataset.commentid;reply_to=$el.dataset.replyto"', $link);
   }

   public function register(): void
   {
      register_post_type('text', [
         'labels' => [
            'name'          => 'Textos',
            'archives'      => 'Textos',
            'singular_name' => 'Texto',
         ],
         'description'   => 'Todos os trabalhos já publicados.',
         'public'        => true,
         'has_archive'   => true,
         'show_in_rest'  => true,
         'menu_position' => 3,
         'menu_icon'     => 'dashicons-text',
         'supports'      => ['title', 'editor', 'author', 'excerpt', 'comments', 'revisions', 'custom-fields', 'page-attributes'],
         'rewrite'       => [
            'slug' => 'texto',
         ],
         'can_export' => false,
         'taxonomies' => [
            'series',
            'club',
         ],
      ]);
   }

   public function set_metatags($metatags)
   {
      if (!is_singular('text')) {
         return $metatags;
      }

      $metatags['og:image'] = get_post_meta(get_the_ID(), 'image_mini', true);

      return $metatags;
   }

   public function set_post_content()
   {
      if (!is_page('publish')) {
         return;
      }

      $localize['edit_url'] = WritersCampPUtils::get_page_link('edit', [
         'edit' => 'ID',
      ]);

      if (!empty($_GET['edit'])) {
         $post = get_post($_GET['edit']);

         if (current_user_can('edit_post', $post->ID)) {
            $localize['post_content'] = $post->post_content;
         }
      }

      wp_localize_script('dashboard', 'editor', $localize);
   }

   public function set_text_draft()
   {
      if (!is_page('publish') || empty($_GET['draft'])) {
         return;
      }

      $post = get_post($_GET['draft']);

      if ((int) $post->post_author !== get_current_user_id()) {
         if (wp_safe_redirect(WritersCampPUtils::get_page_link('dashboard'))) {
            exit;
         }
      }

      $post_ID = wp_update_post([
         'ID'          => $post->ID,
         'post_status' => 'draft',
      ]);

      if (!is_wp_error($post_ID)) {
         if (wp_safe_redirect(WritersCampPUtils::get_page_link('edit', [
            'edit' => $post->ID,
         ]))) {
            exit;
         }
      }
   }
}
