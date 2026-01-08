<?php

namespace writersCampP\Text;

use cavEx\Shortlink\Utils as ShortlinkUtils;
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

      add_action('save_post_text', [$this, 'check_comments'], 10, 2);
      add_action('save_post_text', [$this, 'create_shortlink'], 10, 2);
      add_action('save_post_text', [$this, 'create_share_img'], 15, 2);
      add_action('delete_attachment', [$this, 'on_delete_attachment'], 10, 2);

      add_filter('comment_reply_link', [$this, 'filter_comment_reply_link']);
      add_filter('cav_head_metatags', [$this, 'set_metatags']);

      new Register_Endpoint();
   }

   public function check_comments($post_ID, $post_obj)
   {
      $js_blocks = get_post_meta($post_ID, 'raw_json', true);
      $js_blocks = TextUtils::convert_raw_json($js_blocks);

      if (empty($js_blocks)) {
         return;
      }

      $updated = false;
      $blocks  = parse_blocks($post_obj->post_content);

      $idx = 0;

      foreach ($blocks as $block) {
         if (!$block['blockName']) {
            continue;
         }

         $noteId = $block['attrs']['metadata']['noteId']            ?? false;
         $commId = $js_blocks['content'][$idx]['attrs']['comments'] ?? false;

         if ($noteId !== $commId) {
            $updated = true;

            if ($noteId) {
               $js_blocks['content'][$idx]['attrs']['comments'] = $noteId;
            } else {
               unset($js_blocks['content'][$idx]['attrs']['comments']);
            }
         }

         $idx++;
      }

      if ($updated) {
         update_post_meta($post_ID, 'raw_json', $js_blocks);
      }
   }

   public function create_share_img($post_ID, $post_obj)
   {
      if ('publish' !== $post_obj->post_status) {
         return;
      }

      $delay = get_post_meta($post_ID, '_delay_upload', true);
      $share = get_post_meta($post_ID, 'share_image', true);
      $bg    = get_post_meta($post_ID, 'image_full', true);

      if (!empty($delay) || !empty($share) || empty($bg)) {
         return;
      }

      update_post_meta($post_ID, '_delay_upload', 1);

      $link_ID = get_post_meta($post_ID, 'shortlink', true);
      $author  = get_the_author_meta('display_name', $post_obj->post_author);
      $clubs   = get_the_terms($post_ID, 'club');

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
         $logo       = 'black.png';
      } else {
         $text_color = imagecolorallocate($img, 250, 250, 250);
         $logo       = 'white.png';
      }

      // CLUB PREP
      list($club_color_r, $club_color_g, $club_color_b) = Utils::hex_to_rgb($club_color);

      $club_color = imagecolorallocate($img, $club_color_r, $club_color_g, $club_color_b);

      // TITLE CALC
      $titles  = TextUtils::split_string($post_obj->post_title, 25, 2);
      $title_y = match (count($titles)) {
         1 => 86,
         2 => 185,
      };

      // EXCERPT CALC
      $excerpts  = TextUtils::split_string($post_obj->post_excerpt);
      $excerpt_y = match (count($excerpts)) {
         1 => 1394,
         2 => 1317,
         3 => 1241,
         4 => 1168,
      };
      $club_y = $excerpt_y - $title_y - 170;

      // CLUB PRINT
      ImageUtils::rect($img, 55, $club_y, $club_size, $club_y + 80, $club_color, 20);
      $white = imagecolorallocate($img, 250, 250, 250);
      imagettftext($img, 30, 0, 70, $club_y + 53, $white, $icon, $club_icon);
      imagettftext($img, 30, 0, 120, $club_y + 54, $white, $font_bold, $club);

      // TITLE PRINT
      foreach ($titles as $index => $title) {
         imagettftext($img, 60, 0, 55, ($excerpt_y - $title_y) + 95 * $index, $text_color, $font_bold, $title);
      }

      // EXCERPT PRINT
      foreach ($excerpts as $index => $excerpt) {
         imagettftext($img, 46, 0, 55, $excerpt_y + 75 * $index, $text_color, $font_normal, $excerpt);
      }

      // AUTHOR
      imagettftext($img, 35, 0, 162, 1501, $text_color, $font_normal, $author);
      $avatar = get_avatar_url($post_obj->post_author, [
         'size' => 96,
      ]);

      ImageUtils::circle_crop($img, $avatar, 96, 55, 1439);

      // LOGO
      $logo   = imagecreatefrompng($uploads . $logo);
      $logo_x = imagesx($logo);
      $logo_y = imagesy($logo);
      imagealphablending($img, true);
      imagecopy($img, $logo, 950, 1439, 0, 0, $logo_x, $logo_y);

      // PREP
      $files[] = [
         'name' => "share-{$post_ID}.png",
         'gd'   => $img,
         'key'  => 'share_image',
      ];

      // LINK COPY
      if (!empty($link_ID)) {
         $shorter = ShortlinkUtils::get_link($link_ID);

         $img_link = imagecreatetruecolor(1080, 1920);
         imagecopy($img_link, $img, 0, 0, 0, 0, 1080, 1920);

         $shortlink = str_replace(['://', 'https', 'http'], '', $shorter['link']);
         $qr_code   = imagecreatefrompng($shorter['qr_code']);
         $qr_code_x = imagesx($qr_code);
         $qr_code_y = imagesy($qr_code);
         imagecopy($img_link, $qr_code, 55, 1710, 0, 0, $qr_code_x, $qr_code_y);
         imagettftext($img_link, 30, 0, 227, 1850, $text_color, $font_normal, $shortlink);

         $files[] = [
            'name' => "share_link-{$post_ID}.png",
            'gd'   => $img_link,
            'key'  => 'share_link_image',
         ];
      }

      // SAVE
      if (!function_exists('wp_handle_upload')) {
         require_once ABSPATH . 'wp-admin/includes/file.php';
      }

      if (!function_exists('wp_crop_image')) {
         include ABSPATH . 'wp-admin/includes/image.php';
      }

      \add_filter('intermediate_image_sizes_advanced', '__return_empty_array', 11);

      foreach ($files as $file) {
         $path = $uploads . $file['name'];
         imagepng($file['gd'], $path);

         $to_upload = [
            'name'     => $file['name'],
            'type'     => 'image/png',
            'tmp_name' => $path,
            'size'     => filesize($path),
            'error'    => 0,
         ];
         $overrides = [
            'test_form' => false,
         ];

         $uploaded = \wp_handle_sideload($to_upload, $overrides);

         if (file_exists($path)) {
            unlink($path);
         }

         $attachment_ID = \wp_insert_attachment([
            'guid'           => \wp_upload_dir()['url'] . '/' . $file['name'],
            'post_mime_type' => 'image/png',
            'post_title'     => 'Share ' . $post_ID,
            'post_status'    => 'inherit',
         ], $uploaded['file'], $post_ID);

         $attachment_data = \wp_generate_attachment_metadata($attachment_ID, $uploaded['file']);
         \wp_update_attachment_metadata($attachment_ID, $attachment_data);

         \update_post_meta($post_ID, $file['key'], \wp_get_attachment_url($attachment_ID));
      }

      \remove_filter('intermediate_image_sizes_advanced', '__return_empty_array', 11);

      \delete_post_meta($post_ID, '_delay_upload');
   }

   public function create_shortlink($post_ID, $post_obj)
   {
      if ('publish' !== $post_obj->post_status) {
         return;
      }

      $delay = \get_post_meta($post_ID, '_delay_shortlink', true);

      if (!empty($delay)) {
         return;
      }

      \update_post_meta($post_ID, '_delay_shortlink', 1);

      $link_ID = \get_post_meta($post_ID, 'shortlink', true);

      if (empty($link_ID)) {
         $link_ID = ShortlinkUtils::create_shortlink($post_obj->post_title, \get_permalink($post_ID));
         \update_post_meta($post_ID, 'shortlink', $link_ID);
      } else {
         ShortlinkUtils::update_shortlink($link_ID, \get_permalink($post_ID));
      }

      \delete_post_meta($post_ID, '_delay_shortlink');
   }

   public function filter_comment_reply_link($link)
   {
      return str_replace('class="comment-reply-link"', 'class="comment-reply-link" x-on:click.prevent="parent=$el.dataset.commentid;reply_to=$el.dataset.replyto"', $link);
   }

   public function on_delete_attachment($_post_ID, $post_obj)
   {
      delete_post_meta($post_obj->post_parent, 'share_link_image');
      delete_post_meta($post_obj->post_parent, 'share_image');
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
         'supports'      => ['title', 'editor' => [
            'notes' => true,
         ], 'author', 'excerpt', 'comments', 'revisions', 'custom-fields', 'page-attributes'],
         'rewrite' => [
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
         $post_obj = get_post($_GET['edit']);

         if (current_user_can('edit_post', $post_obj->ID)) {
            $json = get_post_meta($post_obj->ID, 'raw_json', true);
            $json = TextUtils::convert_raw_json($json);

            $localize['raw_json'] = null;

            if (!empty($json)) {
               $localize['raw_json'] = $json;

               $comments_ID = [];

               foreach ($json['content'] as $block) {
                  if (!empty($block['attrs']['comments'])) {
                     $comments_ID[] = $block['attrs']['comments'];
                  }
               }

               if (!empty($comments_ID)) {
                  $raw_comments = get_comments([
                     'comment__in' => $comments_ID,
                     'orderby'     => 'comment_ID',
                     'order'       => 'ASC',
                     'type'        => 'note',
                  ]);

                  foreach ($raw_comments as $comment) {
                     $comments[$comment->comment_ID] = [
                        'comment' => $comment->comment_content,
                        'author'  => $comment->comment_author,
                        'avatar'  => get_avatar($comment->comment_author_email, 32, '', '', [
                           'class' => 'rounded-full',
                        ]),
                     ];
                  }

                  $localize['comments'] = $comments;
               }
            }
         }
      }

      wp_localize_script('dashboard', 'editorG', $localize);
   }

   public function set_text_draft()
   {
      if (!is_page('publish') || empty($_GET['draft'])) {
         return;
      }

      $post_obj = get_post($_GET['draft']);

      if ((int) $post_obj->post_author !== get_current_user_id()) {
         if (wp_safe_redirect(WritersCampPUtils::get_page_link('dashboard'))) {
            exit;
         }
      }

      $post_ID = wp_update_post([
         'ID'          => $post_obj->ID,
         'post_status' => 'draft',
      ]);

      if (!is_wp_error($post_ID)) {
         if (wp_safe_redirect(WritersCampPUtils::get_page_link('edit', [
            'edit' => $post_obj->ID,
         ]))) {
            exit;
         }
      }
   }

   // public function status_changed($post_ID, $post)
   // {
   //    if ('text' !== $post_after->post_type || 'publish' !== $post_after->post_status) {
   //       return;
   //    }

   //    $tts_hash = get_post_meta($post_ID, 'tts_hash', true);
   //    $text     = Utils::text_to_ssml($post_after->post_content);

   //    if (!empty(1)) {
   //    }

   //    // md5($post_after->post_content);

   //    // update_post_meta($post_ID, 'tts_hash', true);
   //    // update_post_meta($post_ID, 'tts_audio', true);
   // }
}
