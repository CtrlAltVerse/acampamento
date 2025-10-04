<?php

namespace writersCampP\Text;

use cavWP\Validate;
use writersCampP\Club\Utils as ClubUtils;

class Utils
{
   public static function get($count = 6, $type = 'popular')
   {
      if ('popular' === $type) {
         $orderby['comment_count'] = 'DESC';
      }

      $orderby['date'] = 'DESC';

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
         'post_content' => [
            'title'     => 'Texto',
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
}
