<?php

namespace App\Http\Middleware;

use App\Traits\ResponseJson;
use App\Utils\WxBizDataCrypt;
use Closure;
use Illuminate\Support\Facades\Redis;

class CheckWxSession
{
    use ResponseJson;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sid = $request->header('p-sid');
        if($sid){
            $rawData = $request->header('rawData');
            $signature = $request->header('signature');
            $hKey = env('WX_REDIS_SESSION_PREFIX') . $sid;



            if($rawData && $signature){
                $sessionKey    = Redis::hget($hKey, 'session_key');
                $signature2    = sha1($rawData . $sessionKey);
                if($signature2 != $signature){
                    return $this->return_json('error', '签名校验失败');
                }
            }
            $expires_in = env('WX_REDIS_SESSION_EXPIRE');
            Redis::expire($hKey, $expires_in);
        }
        return $next($request);
    }
}
