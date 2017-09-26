<?php

namespace App\Http\Controllers;

use App\Mail\BackupData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BackupController extends Controller
{
    //
    public function sendMail($sc){
        if($sc != 'mZq5C015BfrxL1DwNdk3'){
            return 1;
        }

        set_time_limit(0);
        Mail::to('670033395@qq.com')->send(new BackupData());
    }
}
