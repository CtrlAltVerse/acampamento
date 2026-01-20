<?php

namespace writersCampP\Services;

class Utils
{
   public static function base64url($data)
   {
      return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
   }
}
