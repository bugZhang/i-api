<?php
namespace App\Traits;

trait HttpClient{
    private $_connectTimeOut = 50; // 发起连接前的最长等待时间（秒）
    private $_responseTimeOut = 50; // 默认允许执行的最长时间（秒）
    private $_requestHeaders = array ();

    /**
     * 设置请求超时时间
     *
     * @param
     *        	$time
     */
    protected function setTimeOut($time) {
        $this->_responseTimeOut = $time;
    }

    /**
     * 添加单个请求头信息（一次添加一个）
     *
     * @param String $headerStr
     *        	请求头信息
     */
    protected function addRequestHeader($headerStr) {
        $this->_requestHeaders [] = $headerStr;
    }

    /**
     * 发送http请求
     *
     * @param String $url
     * @param string $method
     * @param array $params
     * @param boolean $isJson
     */
    public function doHttpRequest($url, $method = 'get', $params = array(), $isJson = true) {
        if(empty($url)){
            return false;
        }
        $ch = curl_init ();
        if ($isJson) {
            $this->addRequestHeader ( "content-type: application/json; charset=UTF-8" );
        }
        if (! empty ( $this->_requestHeaders )) {
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $this->_requestHeaders );
        }
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $this->_connectTimeOut );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, $this->_responseTimeOut );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );

        switch (strtolower ( $method )) {
            case 'post' :
                curl_setopt ( $ch, CURLOPT_POST, true );
                curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );
                break;
            case 'put' :
                curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
                curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );
                break;
            case 'delete' :
                curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'DELETE' );
                curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );
                break;
            default :
                // 默认是get请求，可以不设置
                // curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
        }
        $file_contents = curl_exec ( $ch );
        //var_dump($file_contents);
// 		$response = curl_getinfo ( $ch );
        curl_close ( $ch );
        $this->_requestHeaders = null;
        return $file_contents;
    }
    protected function doHttpRequestRestFul($url, $params, $method = 'post'){
        if(empty($url)){
            return false;
        }
        $url = self::buildRestFulUrl($url, $params);
        $responseContent = self::doHttpRequest($url, $method, array(), false);
        return $responseContent;
    }

    protected function buildRestFulUrl($domain, $params) {
        $strLength = strlen ( $domain );
        if ($strLength < 1) {
            return false;
        }
        $index = strripos ( $domain, '/' );
        if (($index + 1) != $strLength) {
            $domain = $domain . '/';
        }
        $paramStr = '';
        if (! empty ( $params )) {
            $paramStr = implode ( '/', $params );
        }
        return $domain . $paramStr;
    }
}