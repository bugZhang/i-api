<?php

namespace App\Http\Middleware;

use App\Traits\ResponseJson;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class checkNewBankSession
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
        $request->isNewBank = 1;
        $sid = $request->header('p-sid');
        if($sid){
            $rawData = $request->input('rawData');
            $signature = $request->input('signature');
            $hKey = env('WX_NEW_BANK_REDIS_SESSION_PREFIX') . $sid;
            if($rawData && $signature){
                $sessionKey    = Redis::hget($hKey, 'session_key');
                $signature2    = sha1($rawData . $sessionKey);
                if($signature2 != $signature){
                    return $this->return_json('error', '签名校验失败');
                }
            }
            $expires_in = env('WX_REDIS_SESSION_EXPIRE');
            $request->wx_openid = Redis::hget($hKey, 'openid');
            if(!$request->wx_openid){
                Log::error('缓存中未找到openid');
            }
            Redis::expire($hKey, $expires_in);
        }
        return $next($request);
    }
}
