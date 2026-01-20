<?php

namespace writersCampP\Services;

use WP_Error;

class TextToSpeech
{
   public function __construct() {}

   public function synthesize($voice, $text)
   {
      $request = wp_remote_post('https://texttospeech.googleapis.com/v1/text:synthesize', [
         'headers' => [
            'Content-Type'        => 'application/json',
            'Authorization'       => $this->get_token(),
            'x-goog-user-project' => 'ctrlaltverso',
         ],
         'body' => json_encode([
            'input' => [
               'ssml' => $text,
            ],
            'voice' => [
               'languageCode' => 'pt-BR',
               'name'         => $voice,
            ],
            'audioConfig' => [
               'speakingRate'  => 1.025,
               'audioEncoding' => 'OGG_OPUS',
            ],
         ]),
      ]);

      $body = json_decode(wp_remote_retrieve_body($request), true);

      if (wp_remote_retrieve_response_code($request) !== 200) {
         return new WP_Error($body['error']['code'], $body['error']['message']);
      }

      return $body['audioContent'];
   }

   private function create_jwt(): string
   {
      $header = Utils::base64url(json_encode([
         'alg' => 'RS256',
         'typ' => 'JWT',
      ]));

      $now     = time();
      $payload = Utils::base64url(json_encode([
         'iss'   => GCP_CLIENT_EMAIL,
         'scope' => 'https://www.googleapis.com/auth/cloud-platform',
         'aud'   => 'https://oauth2.googleapis.com/token',
         'iat'   => $now,
         'exp'   => $now + 3600,
      ]));

      $signatureInput = "{$header}.{$payload}";
      $private_key    = str_replace('\n', "\n", GCP_PRIVATE_KEY);
      openssl_sign($signatureInput, $signature, $private_key, 'sha256');

      return $signatureInput . '.' . Utils::base64url($signature);
   }

   private function get_token()
   {
      $token = get_transient('cav-gcp-tts-token');

      if ($token) {
         return "Bearer {$token}";
      }

      $jwt = $this->create_jwt();

      $request = wp_remote_post('https://oauth2.googleapis.com/token', [
         'header' => ['Content-Type' => 'application/x-www-form-urlencoded'],
         'body'   => [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
         ],
      ]);

      $body = json_decode(wp_remote_retrieve_body($request), true);

      if (wp_remote_retrieve_response_code($request) !== 200) {
         return new WP_Error('', 'Request failed', $body);
      }

      $token = $body['access_token'];

      set_transient('cav-gcp-tts-token', $token, time() + 3500);

      return "Bearer {$token}";
   }
}
