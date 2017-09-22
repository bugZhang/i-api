<?php
/**
 * Created by PhpStorm.
 * User: yixina-d
 * Date: 17/9/22
 * Time: 11:43
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class WxUserModel extends Model
{

    protected $table = 'wx_user';
    public $timestamps = false;

    public function selectUserByOpenid($openid){
        if(!$openid){
            return 0;
        }
        $user = $this->where('open_id', $openid)->select()->get();
        return $user && count($user) > 0 ? $user : 0;
    }


    public function addUser($params){

        if($params && $params['open_id']){
            return $this->insert($params);
        }else{
            return 0;
        }
    }
    public function updateUser($params){

        if($params['open_id']){
            $params['last_login_time']  = date('Y-m-d H:i:s', time());
            return $this->where('open_id', $params['open_id'])
                ->update($params);
        }else{
            return 0;
        }

    }

    public function saveOrUpdate($params){

        if(!$params || empty($params['open_id'])){
            return 0;
        }
        $openid = $this->where('open_id', $params['open_id'])->select('open_id')->get();
        if($openid && count($openid) > 0){
            return $this->updateUser($params);
        }else{
            return $this->addUser($params);
        }
    }

}