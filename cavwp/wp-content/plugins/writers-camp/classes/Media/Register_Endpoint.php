<?php

namespace writersCampP\Media;

use cavWP\Services\Unsplash;
use WP_REST_Response;
use WP_REST_Server;

class Register_Endpoint
{
   private $unsplash;

   public function __construct()
   {
      add_action('rest_api_init', [$this, 'create_endpoints']);

      $this->unsplash = new Unsplash();
   }

   public function create_endpoints(): void
   {
      register_rest_route('unsplash/v1', '/search', [
         'methods'             => WP_REST_Server::READABLE,
         'callback'            => [$this, 'get_search'],
         'permission_callback' => '__return_true',
         'args'                => [
            'q' => [
               'type'     => 'string',
               'required' => true,
            ],
            'page' => [
               'type'    => 'integer',
               'default' => 1,
            ],
         ],
      ]);

      register_rest_route('unsplash/v1', '/download', [
         'methods'             => WP_REST_Server::READABLE,
         'callback'            => [$this, 'get_download'],
         'permission_callback' => '__return_true',
         'args'                => [
            'url' => [
               'type'     => 'string',
               'required' => true,
            ],
         ],
      ]);
   }

   public function get_download($request)
   {
      $url = $request->get_param('url');

      $response = $this->unsplash->download($url);

      return new WP_REST_Response($response);
   }

   public function get_search($request)
   {
      $query    = $request->get_param('q');
      $page     = $request->get_param('page');
      $response = $this->unsplash->search($query, $page);
      $results  = [];

      foreach ($response['results'] as $item) {
         $results[] = [
            'width'            => $item['width'],
            'height'           => $item['height'],
            'thumb'            => $item['urls']['thumb'], // para listagem
            'raw'              => $item['urls']['raw'], // para edição
            'download'         => $item['links']['download_location'],
            'image_author'     => $item['user']['name'],
            'image_author_url' => $item['user']['links']['html'] . '?utm_source=CtrlAltVersœ&utm_medium=referral',
         ];
      }

      return new WP_REST_Response([
         'total'    => $response['total'],
         'maxPages' => $response['total_pages'],
         'results'  => $results,
      ]);
   }
}
