<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Use:后台主页面
 * Date: 2016/6/12
 * Time: 11:27
 */
namespace Admin\Controller;
use Think\Controller;

class MainController extends CommonController
{
    //后台主页面
    public function index()
    {
        //超级管理员登录菜单和普通管理员菜单
        if(session(C('ADMIN_AUTH_KEY')))
        {
            //取出所有节点
            $node = D('Node')->where('level = 2')->order('sort')->relation(true)->select();
            foreach($node as $key=>$value)
            {
                if(intval($value['sort']) >= 100)
                {
                    unset($node[$key]);
                }
                //模块存在,比较里面的操作
                foreach($value['node'] as $key1=>$value1)
                {
                    if(intval($value1['sort']) >= 100)
                    {
                        unset($node[$key]['node'][$key1]);
                    }
                }
            }
        }else
        {
            //取出所有节点
            $node = D('Node')->where('level = 2')->order('sort')->relation(true)->select();
            //取出当前登录用户所有模块权限(英文名称)和操作权限(ID)
            $module = '';
            $node_id = '';
            $accessList = $_SESSION['_ACCESS_LIST'];//thinkphp自带的权限session
            foreach($accessList as $key=>$value)
            {
                foreach($value as $key1=>$value1)
                {
                    $module = $module.','.$key1;
                    foreach($value1 as $key2=>$value2)
                    {
                        $node_id = $node_id.','.$value2;
                    }
                }
            }
            //去除没有权限的节点
            foreach($node as $key=>$value)
            {
                if(!in_array(strtoupper($value['name']),explode(',',$module)) || intval($value['sort']) >= 100)
                {
                    unset($node[$key]);
                }else
                {
                    //模块存在,比较里面的操作
                    foreach($value['node'] as $key1=>$value1)
                    {
                        if(!in_array($value1['id'],explode(',',$node_id)) || intval($value1['sort']) >= 100)
                        {
                            unset($node[$key]['node'][$key1]);
                        }
                    }
                }
            }
        }
        $this->assign('node',$node);
        $this->display();
    }


    //main主页面内容
    public function welcome()
    {
        $this->assign('sys_info',$this->get_sys_info());
        $today = strtotime("-1 day");
        /*$count['handle_order'] = M('Orderlist')->where("shipping_status=0")->count();//待发货订单
        $count['new_order'] = M('Orderlist')->where("regtime>$today")->count();//今天新增订单
        $count['goods'] =  M('Orderlist')->where("order_status=2")->count();//已完成订单
        $count['article'] =  M('Goods')->where("gstatus=1")->count();//在售商品
        $count['users'] = M('Member')->where("1=1")->count();//会员总数
        $count['today_login'] = M('Member')->where("logtime>$today")->count();//今日访问
        $count['new_users'] = M('Member')->where("regtime>$today")->count();//新增会员
        $count['comment'] = M('Goods')->where("regtime>$today")->count();//新增商品
        $this->assign('count',$count);*/
        $this->assign('count',array());
        $this->display();
    }

    public function get_sys_info(){
        $sys_info['os']             = PHP_OS;
        $sys_info['zlib']           = function_exists('gzclose') ? 'YES' : 'NO';//zlib
        $sys_info['safe_mode']      = (boolean) ini_get('safe_mode') ? 'YES' : 'NO';//safe_mode = Off
        $sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl']			= function_exists('curl_init') ? 'YES' : 'NO';
        $sys_info['web_server']     = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['phpv']           = phpversion();
        $sys_info['ip'] 			= GetHostByName($_SERVER['SERVER_NAME']);
        $sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
        $sys_info['max_ex_time'] 	= @ini_get("max_execution_time").'s'; //脚本最大执行时间
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['domain'] 		= $_SERVER['HTTP_HOST'];
        $sys_info['memory_limit']   = ini_get('memory_limit');
        $sys_info['version']   	    = C('SHOP_VERSION');
        $mysqlinfo = M()->query("SELECT VERSION() as version");
        $sys_info['mysql_version']  = $mysqlinfo['version'];
        if(function_exists("gd_info")){
            $gd = gd_info();
            $sys_info['gdinfo'] 	= $gd['GD Version'];
        }else {
            $sys_info['gdinfo'] 	= "未知";
        }
        return $sys_info;
    }
}