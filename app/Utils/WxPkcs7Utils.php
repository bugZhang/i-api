<?php
namespace App\Utils;


class WxPkcs7Utils
{

    public static $block_size = 16;

    public $key;

    /**
     * 对需要加密的明文进行填充补位
     * @param $text 需要进行填充补位操作的明文
     * @return 补齐明文字符串
     */
    public function encode( $text )
    {
        $block_size = $this::$block_size;
        $text_length = strlen( $text );
        //计算需要填充的位数
        $amount_to_pad = $this::$block_size - ( $text_length % $this::$block_size );
        if ( $amount_to_pad == 0 ) {
            $amount_to_pad = $this::block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr( $amount_to_pad );
        $tmp = "";
        for ( $index = 0; $index < $amount_to_pad; $index++ ) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }



    public function Prpcrypt( $k )
    {
        $this->key = $k;
    }

    public function decrypt($aesCipher, $aesIV){

        try {
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            mcrypt_generic_init($module, $this->key, $aesIV);
            //解密
            $decrypted = mdecrypt_generic($module, $aesCipher);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);


            openssl_encrypt($aesCipher, $aesCipher, $this->key, $aesIV);


        } catch (Exception $e) {
            return 0;
        }

        try {
            //去除补位字符
            $result = $this->decode($decrypted);

        } catch (Exception $e) {
            return 0;
        }
        return array(0, $result);
    }
}