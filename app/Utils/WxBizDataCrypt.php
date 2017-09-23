<?php
/**
 * Created by PhpStorm.
 * User: yixina-d
 * Date: 17/9/22
 * Time: 19:36
 */

namespace App\Utils;


class WxBizDataCrypt
{

    private $appid;
    private $sessionKey;

    /**
     * error code 说明.
     * <ul>

     *    <li>-41001: encodingAesKey 非法</li>
     *    <li>-41003: aes 解密失败</li>
     *    <li>-41004: 解密后得到的buffer非法</li>
     *    <li>-41005: base64加密失败</li>
     *    <li>-41016: base64解密失败</li>
     * </ul>
     */
    private static $ERR_CODE = [
        'OK' => 0,
        'IllegalAesKey' => -41001,
        'IllegalIv' => -41002,
        'IllegalBuffer' => -41003,
        'DecodeBase64Error' => -41004
    ];

    /**
     * 构造函数
     * @param $sessionKey string 用户在小程序登录后获取的会话密钥
     * @param $appid string 小程序的appid
     */
    public function WXBizDataCrypt( $appid, $sessionKey)
    {
        $this->sessionKey = $sessionKey;
        $this->appid = $appid;
    }


    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData( $encryptedData, $iv, &$data )
    {
        if (strlen($this->sessionKey) != 24) {
            return self::$ERR_CODE['IllegalAesKey'];
        }
        $aesKey=base64_decode($this->sessionKey);

        if (strlen($iv) != 24) {
            return self::$ERR_CODE['IllegalIv'];
        }

        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        $result  =openssl_decrypt($aesCipher, 'AES-128-CBC', $aesKey, $options=OPENSSL_RAW_DATA, $aesIV);

        if($result){
            $result = $this->decode($result);
        }

        $dataObj = json_decode($result);

        if( $dataObj  == NULL )
        {
            return self::$ERR_CODE['IllegalBuffer'];
        }

        if( $dataObj->watermark->appid != $this->appid )
        {
            return self::$ERR_CODE['IllegalBuffer'];
        }
        $data = $dataObj;
        return 1;
    }

    /**
     * 对解密后的明文进行补位删除
     * @param decrypted 解密后的明文
     * @return 删除填充补位后的明文
     */
    function decode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }

}