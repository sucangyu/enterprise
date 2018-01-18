<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Use:每个模块检验登录者权限的公共文件，让其他模块继承CommonController类而不是Controller类
 * Date: 2016/6/14
 * Time: 11:25
 */
namespace Admin\Controller;
use Think\Controller;
use Org\Util\Rbac;

class CommonController extends Controller
{
    //登录验证
    public function _initialize()
    {
        if(!isset($_SESSION[C('USER_AUTH_KEY')]))
        {
            $this->error('登录异常',U('Index/index'),2);
        }
        //无需认证的模块
        $notAuth = in_array('CONTROLLER_NAME ',explode(',',C('NOT_AUTH_MODULE'))) || in_array('ACTION_NAME',explode(',',C('NOT_AUTH_ACTION')));
        if(C('USER_AUTH_ON') && !$notAuth)
        {
            //RBAC认证

            $rbac = new Rbac();
            if(!$rbac->AccessDecision())//验证用户是否拥有相应的权限
            {
                $this->error('您没有访问权限');
            }
        }
        
        $configM = M('Config');
        $configArr = $configM->select();
        $this->store_name = '大周商城';
        $this->site_url = 'http://www.huyan.space';
        if(!empty($configArr))
        {
            foreach($configArr as $key=>$val)
            {
                if($val['name']=='store_name')
                {
                    $this->store_name = $val['value'];
                }
                if($val['name']=='site_url')
                {
                    $this->site_url = $val['value'];
                }
            }
        }
    }
}