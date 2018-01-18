<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27 0027
 * Time: 上午 10:33
 */
namespace Mobile\Logic;
define('APPID','wx40f0420cee68574f');//设置借用appid
define('APPSN','123f0b7a4e312425e21ba92a7054054e');//设置借用apps

class Jssdk2
{
    private $appid;
    private $url;
    private $appsn;
    function __construct($isjie=0,$urlx=''){
        $this->appid=APPID;
        $this->appsn=APPSN;
        $this->url = (empty($urlx))? "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : $urlx;
    }
    public function get_curl($url){
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $data  =  curl_exec($ch);
        curl_close($ch);
        return json_decode($data,1);
    }
    public function post_curl($url,$post=''){
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $data  =  curl_exec($ch);
        curl_close($ch);
        return json_decode($data,1);
    }
    private function get_randstr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    public function get_jsapi_ticket() {

        $data = json_decode(file_get_contents("./jsapi_ticket.json"));
        if ($data->expire_time < time()) {
            $accessToken = $this->get_access_token();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $result =json_decode($this->httpGet($url));
            $ticket = $result->ticket;
            //$ticket = $result["ticket"];
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen("./jsapi_ticket.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }

        return $ticket;
    }
    public function get_access_token(){
        $data = json_decode(file_get_contents("./access_token.json"));
        if ($data->expire_time < time()) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsn;
            $result =json_decode($this->httpGet($url));
            $access_token = $result->access_token;
            //$access_token = $result["access_token"];
            //echo $access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen("./access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    public function get_sign() {
        $jsapi_ticket = $this->get_jsapi_ticket();
        $url = $this->url;
        $timestamp = time();
        $nonceStr =$this->get_randstr();
        $string = "jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     =>  $this->appid,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }



    public  function get_code($redirect_uri, $scope='snsapi_base',$state=1){//snsapi_userinfo
        if($redirect_uri[0] == '/'){
            $redirect_uri = substr($redirect_uri, 1);
        }
        $redirect_uri = urlencode($redirect_uri);
        $response_type = 'code';
        $appid = $this->appid;
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type='.$response_type.'&scope='.$scope.'&state='.$state.'#wechat_redirect';
        header('Location: '.$url, true, 301);
    }
    public  function get_openid($code){
        $grant_type = 'authorization_code';
        $appid = $this->appid;
        $appsn= $this->appsn;
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsn.'&code='.$code.'&grant_type='.$grant_type.'';
        $data =json_decode(file_get_contents($url),1);
        return $data;
    }
    public  function get_user($openid){
        $accessToken = $this->get_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$accessToken}&openid={$openid}&lang=zh_CN";
        $data  = json_decode(file_get_contents($url),1);
        return $data;
    }
    public function get_user1($accessToken,$openid){
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='. $accessToken . '&openid='. $openid .'&lang=zh_CN';
        $data  =json_decode(file_get_contents($url),1);
        return $data;
        //https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN
    }
    public function get_user2($openid){
        $accessToken = $this->get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='. $accessToken . '&openid='. $openid .'&lang=zh_CN';
        $data  =json_decode(file_get_contents($url),1);
        return $data;
        //https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN
    }
    public function get_user3($openid){
        $accessToken = $this->get_access_token();
        //echo $accessToken;
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='. $accessToken . '&openid='. $openid .'&lang=zh_CN';
        $data  =json_decode(file_get_contents($url),1);
        return $data;
    }
}