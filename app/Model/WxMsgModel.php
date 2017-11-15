<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WxMsgModel extends Model{
    protected $table = 'wx_msg';
    public $timestamps = false;

    public function saveMsg(array $msg){
        if(!$msg['id']){
            return 0;
        }
        return $this->insert($msg);
    }
}