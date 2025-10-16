<?php

namespace writersCampP\Text;

use cavWP\ImageUtils;
use cavWP\Utils;
use writersCampP\Text\Utils as TextUtils;
use writersCampP\Utils as WritersCampPUtils;

class Register
{
   public function __construct()
   {
      add_action('init', [$this, 'register']);
      add_action('wp_enqueue_scripts', [$this, 'set_post_content']);
      add_action('template_redirect', [$this, 'set_text_draft']);
      // add_action('post_updated', [$this, 'status_changed'], 10, 2);
      add_action('post_updated', [$this, 'create_share_img'], 10, 2);

      add_filter('comment_reply_link', [$this, 'filter_comment_reply_link']);
      add_filter('cav_head_metatags', [$this, 'set_metatags']);

      new Register_Endpoint();
   }

   public function create_share_img($post_ID, $post)
   {
      if ('publish' !== $post->post_status) {
         return;
      }

      $share = get_post_meta($post_ID, 'share_image', true);
      $bg    = get_post_meta($post_ID, 'image_full', true);

      if (!empty($share) || empty($bg)) {
         return;
      }

      $author = get_the_author_meta('display_name', $post->post_author);
      $clubs  = get_the_terms($post_ID, 'club');

      if (empty($clubs)) {
         return;
      }

      $club        = mb_strtoupper($clubs[0]->name);
      $club_color  = get_term_meta($clubs[0]->term_id, 'color', true);
      $club_size   = get_term_meta($clubs[0]->term_id, 'size', true);
      $club_icon   = get_term_meta($clubs[0]->term_id, 'char', true);
      $uploads     = wp_upload_dir()['basedir'] . '/';
      $font_bold   = $uploads . 'fonts/KumbhSans-Bold.ttf';
      $font_normal = $uploads . 'fonts/KumbhSans-Regular.ttf';
      $icon        = $uploads . 'fonts/remixicon.ttf';

      $img = @imagecreatetruecolor(1080, 1920);
      imagealphablending($img, false);
      imagesavealpha($img, true);

      // BG
      $bg_img  = @imagecreatefromjpeg($bg);
      $start_y = 0;
      $start_x = floor(imagesx($bg_img) * 0.15);
      $height  = imagesy($bg_img);
      $width   = ceil((1080 / 1920) * $height);

      imagecopyresized($img, $bg_img, 0, 0, $start_x, $start_y, 1080, 1920, $width, $height);
      imagedestroy($bg_img);

      if (get_post_meta($post_ID, 'color', true)) {
         $text_color = imagecolorallocate($img, 23, 23, 23);
         $shadow     = imagecolorallocate($img, 250, 250, 250);
         $logo       = 'black.png';
      } else {
         $text_color = imagecolorallocate($img, 250, 250, 250);
         $shadow     = imagecolorallocate($img, 23, 23, 23);
         $logo       = 'white.png';
      }

      $logo   = imagecreatefrompng($uploads . $logo);
      $logo_x = imagesx($logo);
      $logo_y = imagesy($logo);

      // CLUB PREP
      list($club_color_r, $club_color_g, $club_color_b) = Utils::hex_to_rgb($club_color);

      $club_color = imagecolorallocate($img, $club_color_r, $club_color_g, $club_color_b);

      // TITLE CALC
      $titles  = TextUtils::split_string($post->post_title, 25, 2);
      $title_y = match (count($titles)) {
         1 => 86,
         2 => 185,
      };

      // EXCERPT CALC
      $excerpts = TextUtils::split_string($post->post_excerpt);

      $excerpt_y = match (count($excerpts)) {
         1 => 1394,
         2 => 1317,
         3 => 1241,
         4 => 1168,
      };

      $club_y = $excerpt_y - $title_y - 170;

      // CLUB PRINT
      ImageUtils::rect($img, 55, $club_y, $club_size, $club_y + 80, $club_color, 20);

      imagettftext($img, 30, 0, 70, $club_y + 53, $text_color, $icon, $club_icon);
      imagettftext($img, 30, 0, 120, $club_y + 54, $text_color, $font_bold, $club);

      // TITLE PRINT
      foreach ($titles as $index => $title) {
         imagettftext($img, 60, 0, 57, ($excerpt_y - $title_y + 2) + 95 * $index, $shadow, $font_bold, $title);
         imagettftext($img, 60, 0, 55, ($excerpt_y - $title_y) + 95 * $index, $text_color, $font_bold, $title);
      }

      // EXCERPT PRINT
      foreach ($excerpts as $index => $excerpt) {
         imagettftext($img, 46, 0, 56, ($excerpt_y + 1) + 75 * $index, $shadow, $font_normal, $excerpt);
         imagettftext($img, 46, 0, 55, $excerpt_y + 75 * $index, $text_color, $font_normal, $excerpt);
      }

      // AUTHOR
      imagettftext($img, 35, 0, 163, 1502, $shadow, $font_normal, $author);
      imagettftext($img, 35, 0, 162, 1501, $text_color, $font_normal, $author);
      $avatar = get_avatar_url($post->post_author, [
         'size' => 55,
      ]);

      ImageUtils::circle_crop($img, $avatar, 55, 1439);
      imagecopy($img, $logo, 950, 1439, 0, 0, $logo_x, $logo_y);

      // SAVE
      $file = "share-{$post_ID}.png";
      imagepng($img, $uploads . $file);

      if (!function_exists('wp_handle_upload')) {
         require_once ABSPATH . 'wp-admin/includes/file.php';
      }

      $to_upload = [
         'name'     => $file,
         'type'     => 'image/png',
         'tmp_name' => $uploads . $file,
         'size'     => filesize($uploads . $file),
         'error'    => 0,
      ];
      $overrides = [
         'test_form' => false,
      ];

      function remove_image_sizes($sizes, $metadata)
      {
         return [];
      }

      add_filter('intermediate_image_sizes_advanced', '__return_empty_array', 9);

      $uploaded = \wp_handle_sideload($to_upload, $overrides);

      remove_filter('intermediate_image_sizes_advanced', '__return_empty_array', 9);

      unlink($uploads . $file);

      update_post_meta($post_ID, 'share_image', $uploaded['url']);
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
