<?php

namespace App\Http\Middleware;

class AutStaticToken
{
  private $static_token = 'PVMoLzcJXfhUK1UHuvRrkgupfPoZKFbT';
  public function handle($request, $next)
  {
      $token = $request->bearerToken();
      if ($token===$this->static_token) {
          return $next($request);
      }
      abort(403, 'Unauthorized action.');
  }
}
