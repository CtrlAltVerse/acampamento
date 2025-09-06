<?php

namespace writersCampP\Challenge;

class Register
{
   public function __construct()
   {
      add_action('init', [$this, 'register']);
      add_action('admin_head', [$this, 'set_excerpt_rows']);

      add_filter('pre_get_posts', [$this, 'filter_query']);
      add_filter('cavwp_post_get', [$this, 'filter_excerpt'], 10, 3);

      add_filter('acf/fields/relationship/query/name=texts', [$this, 'filter_texts']);
      add_filter('acf/update_value/name=texts', [$this, 'update_field'], 10, 2);
   }

   public function filter_excerpt($value, $key, $Post)
   {
      if ('post_excerpt' !== $key || 'challenge' !== $Post->post_type) {
         return $value;
      }

      $value = str_replace('1. ', '<ol class="tag-list">1. ', $value);

      if (str_contains($value, '4. ')) {
         $value .= '</ol>';
      }

      $value = preg_replace('/\d\. ([ :\w\dÀ-ÿ]+);?/', '<li class="tag-list-item">$1</li>', $value);

      $value = preg_replace('/yt:([^"&?\/\s]{11})/', '<span class="tag-youtube" x-html="await getYoutube(\'$1\')" data-force="true">$1</span>', $value);

      return preg_replace('/%([-\w=?]+)%/', ' <span class="tag-random" x-countdown:5.start.repeat.invisible="$el.textContent = await getRandom(\'$1\')" data-force="true">$1</span> ', $value);
   }

   public function filter_query($query)
   {
      if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('challenge')) {
         return $query;
      }

      $query->set('meta_query', [[
         'key'     => 'text_count',
         'compare' => '=',
         'value'   => 4,
         'type'    => 'NUMERIC',
      ]]);

      return $query;
   }

   public function filter_texts($field_query)
   {
      $field_query['order']      = 'DESC';
      $field_query['orderby']    = 'date';
      $field_query['meta_query'] = [[
         'key'     => 'challenge',
         'compare' => 'NOT EXISTS',
      ]];

      return $field_query;
   }

   public function register(): void
   {
      register_post_type('challenge', [
         'labels' => [
            'name'          => 'Desafios',
            'archives'      => 'Desafios',
            'singular_name' => 'Desafio',
         ],
         'description'         => 'Todos os Desafios já propostos.',
         'public'              => true,
         'exclude_from_search' => true,
         'has_archive'         => true,
         'menu_position'       => 3,
         'menu_icon'           => 'dashicons-yes-alt',
         'supports'            => ['title', 'author', 'excerpt','custom-fields'],
         'rewrite'             => [
            'slug'  => 'desafios',
            'pages' => true,
         ],
      ]);
   }

   public function set_excerpt_rows()
   {
      $screen = get_current_screen();

      if ('post' !== $screen->base || 'challenge' !== $screen->id) {
         return;
      }

      ?>
<script>
         document.addEventListener('DOMContentLoaded', () => {
            document.getElementById("excerpt").rows = 10
            document.getElementById("excerpt").style.height = 'auto'
         })
      </script>
<?php

   }

   public function update_field($value, $post_ID)
   {
      $count   = 0;
      $publish = 0;

      if (is_array($value)) {
         $count   = count($value);
         $publish = count(array_filter($value, fn($post_id) => 'publish' === get_post_status($post_id)));
      }

      update_post_meta($post_ID, 'text_count', $count);
      update_post_meta($post_ID, 'publish_count', $publish);

      return $value;
   }
}
?>
