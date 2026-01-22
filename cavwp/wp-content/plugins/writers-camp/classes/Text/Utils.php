<?php

namespace writersCampP\Text;

use cavWP\Validate;
use writersCampP\Club\Utils as ClubUtils;

class Utils
{
   public static function convert_raw_json($value)
   {
      if (empty($value) || is_null($value)) {
         return $value;
      }

      if (gettype($value) === 'array') {
         return $value;
      }

      return json_decode(json_encode($value), true);
   }

   public static function get($count = 6, $type = 'popular')
   {
      $orderby['date'] = 'DESC';

      if ('popular' === $type) {
         $orderby['comment_count'] = 'DESC';
      }

      return get_posts([
         'post_type'      => 'text',
         'posts_per_page' => $count,
         'orderby'        => $orderby,
      ]);
   }

   public static function get_comment_fields()
   {
      $Validade = new Validate();

      return [
         'comment_post_ID' => [
            'required'          => true,
            'type'              => 'integer',
            'format'            => 'post:text',
            'minimum'           => 1,
            'validate_callback' => [$Validade, 'check'],
         ],
         'comment_parent' => [
            'required'          => true,
            'type'              => 'integer',
            'minimum'           => 0,
            'format'            => 'comment:comment',
            'validate_callback' => [$Validade, 'check'],
         ],
         'comment' => [
            'title'     => 'Comentário',
            'required'  => true,
            'type'      => 'string',
            'maxLength' => 600,
         ],
      ];
   }

   public static function get_text_fields($format = 'draft')
   {
      $Validade = new Validate();

      $fields = [
         'post_title' => [
            'title'     => 'Título',
            'type'      => 'string',
            'minLength' => 3,
            'required'  => true,
         ],
         'post_excerpt' => [
            'title'       => 'Sumário',
            'description' => 'Curta introdução ao texto',
            'type'        => 'string',
            'minLength'   => 3,
         ],
         'raw_json' => [
            'title'     => 'Blocos',
            'type'      => 'string',
            'required'  => true,
            'minLength' => 3,
            'maxLength' => 333333,
         ],
         'ID' => [
            'type'              => 'integer',
            'format'            => 'post:text',
            'minimum'           => 0,
            'validate_callback' => [$Validade, 'check'],
         ],
         'challenge' => [
            'title'             => 'Desafio',
            'type'              => 'integer',
            'format'            => 'post:challenge',
            'minimum'           => 0,
            'validate_callback' => [$Validade, 'check'],
         ],
         'slot' => [
            'type'    => 'integer',
            'minimum' => 0,
            'maximum' => 3,
         ],
         'club' => [
            'title'  => 'Guilda',
            'type'   => 'integer',
            'format' => 'term:club',
            'enum'   => ClubUtils::get(true),
         ],
         'color' => [
            'type'    => 'boolean',
            'default' => '0',
         ],
         'image_author' => [
            'type'      => 'string',
            'minLength' => 1,
         ],
         'image_author_url' => [
            'type'              => 'string',
            'format'            => 'url',
            'validate_callback' => [$Validade, 'check'],
         ],
         'image_full' => [
            'type'              => 'string',
            'format'            => 'url',
            'validate_callback' => [$Validade, 'check'],
         ],
         'image_mini' => [
            'type'              => 'string',
            'format'            => 'url',
            'validate_callback' => [$Validade, 'check'],
         ],
      ];

      if ('pending' === $format) {
         $fields['post_excerpt']['required']     = true;
         $fields['post_content']['minLength']    = 444;
         $fields['club']['required']             = true;
         $fields['color']['required']            = true;
         $fields['image_author']['required']     = true;
         $fields['image_author_url']['required'] = true;
         $fields['image_full']['required']       = true;
         $fields['image_mini']['required']       = true;
      }

      return $fields;
   }

   public static function json_to_block($block, $parent_tag = '')
   {
      $text    = $block['text']    ?? null;
      $type    = $block['type']    ?? null;
      $content = $block['content'] ?? null;
      $attrs   = self::parse_attrs($block['attrs'] ?? false, $type);

      if (is_array($content)) {
         $content = implode('', array_map(fn($sub_block) => self::json_to_block($sub_block, $type), $content));
      }

      if (empty($content)) {
         $content = '';
      }

      switch ($type) {
         case 'paragraph':
            if ('listItem' === $parent_tag) {
               return $content;
            }

            $alignP = $block['attrs']['textAlign'] ?? 'justify';

            return <<<HTML
            <!-- wp:paragraph {$attrs} -->
            <p class="has-text-align-{$alignP}">{$content}</p>
            <!-- /wp:paragraph -->
            HTML;

         case 'doc':
            return $content;

         case 'text':
            $marks = $block['marks'] ?? [];
            $tags  = [];

            if (\array_find($marks, fn($i) => 'bold' === $i['type'])) {
               $tags[] = 'strong';
            }

            if (\array_find($marks, fn($i) => 'italic' === $i['type'])) {
               $tags[] = 'em';
            }

            if (\array_find($marks, fn($i) => 'strike' === $i['type'])) {
               $tags[] = 's';
            }

            if (\array_find($marks, fn($i) => 'superscript' === $i['type'])) {
               $tags[] = 'sup';
            }

            if (\array_find($marks, fn($i) => 'subscript' === $i['type'])) {
               $tags[] = 'sub';
            }

            if (\array_find($marks, fn($i) => 'code' === $i['type'])) {
               $tags[] = 'code';
            }

            $prefix = '';
            $suffix = '';

            if (count($tags)) {
               $prefix .= implode('', array_map(fn($tag) => "<{$tag}>", $tags));
               $suffix .= implode('', array_map(fn($tag) => "</{$tag}>", array_reverse($tags)));
            }

            if (\array_find($marks, fn($i) => 'underline' === $i['type'])) {
               $prefix .= '<span style="text-decoration: underline;">';
               $suffix = '</span>' . $suffix;
            }

            return $prefix . $text . $suffix;

         case 'heading':
            $alignH = $block['attrs']['textAlign'] ?? 'left';
            $level  = $block['attrs']['level']     ?? 2;

            return <<<HTML
            <!-- wp:heading {$attrs} -->
            <h{$level} class="wp-block-heading has-text-align-{$alignH}">{$content}</h{$level}>
            <!-- /wp:heading -->
            HTML;

         case 'blockquote':
            return <<<HTML
            <!-- wp:quote {$attrs} -->
            <blockquote class="wp-block-quote">{$content}</blockquote>
            <!-- /wp:quote -->
            HTML;

         case 'bulletList':
         case 'orderedList':
            $isOrdered = 'orderedList' === $type;
            $tagList   = $isOrdered ? 'ol' : 'ul';

            return <<<HTML
            <!-- wp:list {$attrs} -->
            <{$tagList} class="wp-block-list">{$content}</{$tagList}>
            <!-- /wp:list -->
            HTML;

         case 'listItem':
            return <<<HTML
            <!-- wp:list-item -->
            <li>{$content}</li>
            <!-- /wp:list-item -->
            HTML;

         case 'codeBlock':
            return <<<HTML
            <!-- wp:code {$attrs} -->
            <pre class="wp-block-code"><code>{$content}</code></pre>
            <!-- /wp:code -->
            HTML;

         case 'horizontalRule':
            return <<<'HTML'
            <!-- wp:separator $attrs -->
            <hr class="wp-block-separator"/>
            <!-- /wp:separator -->
            HTML;

         default:
            debug($block);

            return '';
      }
   }

   public static function json_to_ssml($block, $parent_tag = '')
   {
      $text    = $block['text']    ?? null;
      $type    = $block['type']    ?? null;
      $content = $block['content'] ?? null;

      if (is_array($content)) {
         $content = array_map(fn($sub_block) => self::json_to_ssml($sub_block, $type), $content);

         if (!empty($parent_tag)) {
            $content = implode('', $content);
         }
      }

      if (empty($content)) {
         $content = '';
      }

      switch ($type) {
         case 'paragraph':
            if ('listItem' === $parent_tag) {
               return $content;
            }

            return <<<HTML
            <p>{$content}</p>
            HTML;

         case 'doc':
            return $content;

         case 'text':
            $marks = $block['marks'] ?? [];

            $prefix = '';
            $suffix = '';

            // STRONG
            if (\array_find($marks, fn($i) => 'bold' === $i['type']) || \array_find($marks, fn($i) => 'underline' === $i['type'])) {
               $prefix .= "<emphasis level='strong'>";
               $suffix = '</emphasis>' . $suffix;
            }

            // REDUCE
            if (
               \array_find($marks, fn($i) => 'strike' === $i['type']) || \array_find($marks, fn($i) => 'superscript' === $i['type']) || \array_find($marks, fn($i) => 'subscript' === $i['type'])) {
               $prefix .= "<emphasis level='reduced'>";
               $suffix = '</emphasis>' . $suffix;
            }

            return $prefix . self::parse_text($text) . $suffix;

         case 'heading':
            return <<<HTML
            <break time='2s' />
            <p><emphasis level='moderate'>{$content}</emphasis></p>
            <break time='1s' />
            HTML;

         case 'blockquote':
            return <<<HTML
            <p>{$content}</p>
            HTML;

         case 'bulletList':
         case 'orderedList':
            return <<<HTML
            <p>{$content}</p>
            HTML;

         case 'listItem':
            return <<<HTML
            <s>{$content}</s>
            HTML;

         case 'codeBlock':
            return $content;

         case 'horizontalRule':
            return <<<'HTML'
            <break time='2s' />
            HTML;

         default:
            debug($block);

            return '';
      }
   }

   public static function parse_attrs($attrs, $type)
   {
      if (empty($attrs)) {
         return '';
      }

      if (empty($attrs['textAlign'])) {
         if ('paragraph' === $type) {
            $attrs['align'] = 'justify';
         }

         if ('heading' === $type) {
            $attrs['align'] = 'left';
         }
      }

      if (empty($attrs['level'])) {
         if ('heading' === $type) {
            $attrs['level'] = 2;
         }
      }

      if ('orderedList' === $type) {
         $attrs['ordered'] = true;
      }

      if (!empty($attrs['comments'])) {
         $attrs['metadata']['noteId'] = $attrs['comments'];
      }

      if (!empty($attrs['textAlign'])) {
         $attrs['align'] = $attrs['textAlign'];
      }

      unset($attrs['comments'], $attrs['textAlign']);

      if (empty($attrs)) {
         return '';
      }

      return json_encode($attrs);
   }

   public static function parse_text($text)
   {
      $text = str_replace('&', '&amp;', htmlspecialchars($text, ENT_XHTML | ENT_QUOTES | ENT_SUBSTITUTE));
      $text = str_replace(['...', '…'], "<break strength='medium' />", $text);

      return str_replace(['—'], "<break strength='weak' />", $text);
   }

   public static function split_string($string, $max_char_length = 32, $max_lines = 4)
   {
      $string = trim($string);
      $total  = mb_strlen($string);

      if ($total <= $max_char_length) {
         return [$string];
      }

      $current_line = 0;
      $current_word = 0;
      $done         = 0;
      $words        = explode(' ', $string);

      while ($done < $total && $current_line < $max_lines) {
         $output = '';

         while (isset($words[$current_word]) && mb_strlen($output) + mb_strlen($words[$current_word]) + 1 < $max_char_length) {
            if (!empty($output)) {
               $output .= ' ';
            }

            $output .= $words[$current_word];
            $current_word++;
         }

         $done += mb_strlen($output) + 1;
         $return[$current_line] = $output;

         if ($done < $total && $current_line + 1 === $max_lines) {
            $return[$current_line] .= '...';
         }

         $current_line++;
      }

      return $return;
   }

   public static function text_to_ssml($content)
   {
      // Clean <p>
      $content = preg_replace('/(\<p)([ a-z=\-:;"]+)(\>)/', '$1$3', $content);

      $content = self::parse_text($content);

      $content = explode("\n", $content);

      return array_filter($content);
   }
}
