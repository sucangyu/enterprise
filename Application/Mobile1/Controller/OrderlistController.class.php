<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/20 0020
 * Time: 下午 4:24
 */
namespace Mobile\Controller;

class OrderlistController extends CommonController
{
    public $pre_session;
    public $mid;
    public function _initialize()
    {
        parent::_initialize();
        $this->pre_session = C('SESSION_PRE');
        $this->mid = $_SESSION[$this->pre_session.'user']['id']; //用户id
    }
    //订单列表首页
    public function index()
    {
        $pays = 2;
        if(isset($_GET['pays']))
        {
            $pays = $_GET['pays'];
        }
        if(!in_array($pays,array(0,1,2)))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('非法请求');window.location.href='".U('User/index')."';</script>";
            exit;
        }
        $this->pays = $pays;
        cookie('is_end',0);
        $this->display();
    }

    //ajax订单列表
    public function ajaxindex()
    {
        $pays = 2;
        if(isset($_POST['pays']))
        {
            $pays = intval($_POST['pays']);
        }
        $page = 1;
        if(isset($_POST['page']))
        {
            $page = intval($_POST['page']);
        }
        if(!in_array($pays,array(0,1,2)))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('非法请求');window.location.href='".U('User/index')."';</script>";
            exit;
        }
        $orderlistM = M('Orderlist');
        $w['pay_status'] = $pays;
        if($pays == 2 )
        {
            unset($w['pay_status']);
        }
        $w['mid'] = $this->mid;
        $this->orderArr = $orderlistM->where($w)->order('order_status asc ')->page($page,4)->select();
        if(count($this->orderArr)<4)
        {//前端没有更多提示用
            cookie('is_end',1);
        }
        $this->pays = $pays;
        $this->display();
    }
    //取消订单方法
    public function del()
    {
        $jurl = U('Orderlist/index');
        if(!isset($_REQUEST['order_id'])||empty($_REQUEST['order_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误');window.location.href='".$jurl."';</script>";
            exit;
        }
        M('Orderlist')->where('id='.$_REQUEST['order_id'])->save(array('order_status' => 5));
        $this->display('index');
    }
}