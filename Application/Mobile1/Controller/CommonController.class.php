<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27 0027
 * Time: 上午 10:03
 */
namespace Mobile\Controller;
use Think\Controller;
use Mobile\Logic;

class CommonController extends Controller
{
    public $user = array();
    public $user_id = 0;
    /*
     * 初始化操作
     */
    public function _initialize()
    {
        // 微信Jssdk 操作类 用分享朋友圈 JS
        $jssdk = new \Mobile\Logic\Jssdk2();
        $wx = $jssdk->get_sign();
        $this->assign('signPackage', $wx);

        $session_pre = C('SESSION_PRE');
        $_SESSION[$session_pre.'openid'] = 'o3b7N1G1mQOk5j08stm0pCUNeBRw';
        // 判断当前用户是否手机
        if(!isMobile())
        {
            exit("<meta charset='utf-8'><script type='text/javascript'>alert('请在手机微信端登录')</script>");
        }

        if(!isset($_SESSION[$session_pre.'openid'])||empty($_SESSION[$session_pre.'openid']))
        {
            if(isset($_GET["code"])){
                $code = $_GET["code"];
                $arr = $jssdk->get_openid($code);
                $access_token = $arr["access_token"];
                $openid = $arr["openid"];
                $map['openid'] = $openid;
                $user = M('Member')->where($map)->find();
                $userinfo = $jssdk->get_user1($access_token,$openid);//通过openid获取用户信,有可能会弹出窗口
                if(empty($user))
                {
                    $data = array(
                        'openid'=>$userinfo['openid'],//支付宝用户号
                        'nickname'=>$userinfo['nickname'],
                        'sex'=>$userinfo['sex'],
                        'userimage'=>$userinfo['headimgurl'],
                        'regtime'=>time(),
                    );
                    $user_id = M('Member')->add($data);
                    $user  = M('Member')->find($user_id);
                }else
                {
                    $data = array(
                        'nickname'=>$userinfo['nickname'],
                        'sex'=>$userinfo['sex'],
                        'userimage'=>$userinfo['headimgurl'],
                    );
               
                    M('Member')->where('id='.$user['id'])->save($data);
                    $user  = M('Member')->find($user['id']);
                }
                $_SESSION[$session_pre.'openid']=$openid;
                //var_dump($userinfo);
            }else{
                //$weixin->get_code("http://weixin10.sinaapp.com/jssdk-demo.php","snsapi_base");
                $gerurl = C('RETURN_URL');
				$gerurl='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
				
                $jssdk->get_code($gerurl,"snsapi_userinfo");
                //获取code,	snsapi_base不弹出, 弹出为snsapi_userinfo
            }
        }else
        {
            $map['openid'] = $_SESSION[$session_pre.'openid'];
            $user = M('Member')->where($map)->find();
        }
        session($session_pre.'user',$user);
        session('userimage',$user['userimage']);
        session($session_pre.'user_id',$user['id']);
        //获取配置信息
        $this->memberArr = $user;
        $this->shop_config = getConfig('shop_info');
    }

    //根据id返回地址名称
    public function memaddress($province,$city,$district)
    {
        $model_region = M('Region');
        $temp_arr = array();
        if(!empty($province))
            $temp_arr['province'] = $model_region->where('id='.$province)->getField('name');
        if(!empty($city))
            $temp_arr['city'] = $model_region->where('id='.$city)->getField('name');
        if(!empty($district))
            $temp_arr['district'] = $model_region->where('id='.$district)->getField('name');
        return $temp_arr;
    }

}