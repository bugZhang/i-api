<?php

namespace App\Http\Controllers;

use App\Mail\BackupData;
use App\Model\BankModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
    //
    public function test(){
//        Redis::set('asdf', 'asd11111f');
//       echo  Redis::get('asdf');
        DB::connection()->enableQueryLog();

        $bankModel  = new BankModel();
        $res = $bankModel->selectBanksByNameAndArea(102, '香港', '山东省', '青岛市');
        $log = DB::getQueryLog();
        var_dump($log);
    }
}
