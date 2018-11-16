<?php

namespace App\Http\Controllers;

use App\Model\BankModel;
use App\Model\WxBankCollectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{
    private $newBanks = [
        103 => '中国农业银行',
        102 => '中国工商银行',
        104 => '中国银行',
        105 => '中国建设银行',
        403 => '中国邮政储蓄银行',
        301 => '交通银行',
        308 => '招商银行',
        302 => '中信银行',
        305 => '中国民生银行',
        309 => '兴业银行',
        303 => '中国光大银行',
        310 => '上海浦东发展银行',
        304 => '华夏银行',
        306 => '广发银行',
        307 => '平安银行',
        316 => '浙商银行',
        317 => '农村合作银行',
    ];

    public function getNewBanks(Request $request, $bankCode, $province, $keyword, $page = 1){
        $openid = $request->wx_openid;

        if(!isset($this->newBanks[$bankCode])){
            return $this->return_json('error', '参数错误！');
        }

        $bankModel  = new BankModel();
        $banks = $bankModel->selectNewBanksByAreaAndKeyword($bankCode, $keyword, $province, $page);

        if($banks){
            $returnData = [];
            if($openid){
                $wxCollectModel = new WxBankCollectModel();
                $collectCodes = $wxCollectModel->selectCollectBankCodeByOpenid($openid);
                if($collectCodes){
                    foreach ($collectCodes as $coll){
                        $returnData['collects'][] = $coll->bank_code;
                    }
                }
            }
            foreach ($banks as $bank){
                $temp = [];
                $temp['bank_name'] = $bank->bank_name;
                $temp['bank_code'] = $bank->bank_code;
                $temp['branch_bank_code'] = $bank->branch_bank_code;
                $temp['branch_bank_short_name'] = preg_replace("/^" . $this->newBanks[$bankCode] . "/", '', $bank->branch_bank_name, 1);
                $temp['branch_bank_short_name'] = preg_replace("/^股份有限公司/", '', $temp['branch_bank_short_name'], 1);

                $temp['branch_bank_name'] = $bank->branch_bank_name;

                $temp['id'] = $bank->id;
                if(isset($returnData['collects']) && in_array($bank->branch_bank_code, $returnData['collects'])){
                    $temp['is_collect'] = 1;
                }else{
                    $temp['is_collect'] = 0;
                }

                $data[] = $temp;
            }
            $returnData['banks'] = $data;
            $returnData['count'] = $banks->count();

            return $this->return_json('success', $returnData);

        }else{
            return $this->return_json('error', '未查询到结果');
        }
    }

    public function getBanks(Request $request, $bankCode, $province, $keyword, $page = 1){

        $openid = $request->wx_openid;

        $bankModel  = new BankModel();
        $banks = $bankModel->selectBanksByNameAndArea($bankCode, $keyword, $province, $page);
        if($banks){
            $returnData = [];
            if($openid){
                $wxCollectModel = new WxBankCollectModel();
                $collectCodes = $wxCollectModel->selectCollectBankCodeByOpenid($openid);
                if($collectCodes){
                    foreach ($collectCodes as $coll){
                        $returnData['collects'][] = $coll->bank_code;
                    }
                }
            }
            foreach ($banks as $bank){
                $temp = [];
                $temp['name'] = $bank->name;
                $temp['address'] = $bank->address;
                $temp['code'] = $bank->code;
                $temp['id'] = $bank->id;
                if(isset($returnData['collects']) && in_array($bank->code, $returnData['collects'])){
                    $temp['is_collect'] = 1;
                }else{
                    $temp['is_collect'] = 0;
                }

                $data[] = $temp;
            }
            $returnData['banks'] = $data;
            $returnData['count'] = $banks->count();

            return $this->return_json('success', $returnData);

        }else{
            return $this->return_json('error', '未查询到结果');
        }

    }

    public function getBannerImgs(){

        $songs = ['该乐观起来，这世界有四季与远方还有火锅与理想',
            '你不知道一个一见你就笑的人有多喜欢你', '抱最大的希望，为最大的努力，做最坏的打算',
            '你不能左右天气，但你能转变你的心情', '路漫漫其修远今，吾将上下而求索',
            '生活的道路一旦选定，就要勇敢地走到底，决不回头', '年轻是我们唯一拥有权利去编织梦想的时光',
            '快乐要懂得分享，才能加倍的快乐', '要克服生活的焦虑和沮丧，得先学会做自己的主人',
            '纯洁的思想，可使最微小的行动高贵起来', '只要有信心，人永远不会挫败', '快乐不是因为得到的多而是因为计较的少',
            '如果心胸不似海，又怎能有海一样的事业', '小时候画在手上的表没有动，却带走了我们最好的时光', '会拐弯的小溪，才能最终流向大海',
            '心有所住，即为非住。应无所住而生其心', '多欲为苦，生死疲劳，从贪欲起，少欲无为，身心自在', '我曾经豪情万丈，归来却空空的行囊',
            '最美的不是下雨天，是曾与你躲过雨的屋檐', '从来没有一种坚持会被辜负'];


        $seed = rand(0, (count($songs) - 1));
        $song = $songs[$seed];

        $miniPrograms = [
            ['img' => 'https://wx-api.kelenews.com/api/wx/img/get/random', 'appid' => 'wx02e7e18379ac94fd'],
            ['img' => 'https://wx-api.kelenews.com/api/wx/img/get/random', 'appid' => 'wxde5f48f5fecac8eb'],
            ['img' => 'https://wx-api.kelenews.com/api/wx/img/get/random', 'appid' => 'wxde5f48f5fecac8eb'],
        ];

        return $this->return_json('success', ['song' => $song, 'banners' => $miniPrograms]);

    }
}
