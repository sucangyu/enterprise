<?php
/**
 * Created by PhpStorm.
 * User: Huyh
 * Date: 2017/10/31 0031
 * Time: 下午 1:21
 */
namespace Mobile\Controller;
class ClaimController extends CommonController
{
    public $pre_session;
    public $mid;
    public function _initialize()
    {
        parent::_initialize();
        $this->pre_session = C('SESSION_PRE');
        $this->mid = $_SESSION[$this->pre_session.'user']['id']; //用户id
    }

    //认领列表
    public function index()
    {
        cookie('is_end',0);
        $this->display();
    }

    //认领列表ajax分页
    public function ajaxindex()
    {
        $page = 1;
        if(isset($_POST['page']))
        {
            $page = intval($_POST['page']);
        }
        $claimlistM = D('Claimlist');
        $w['isdel'] = 0;//实际拥有的数量要大于0
        $w['mid'] = $this->mid;
        $w['kind'] = 1;
        $this->claimArr = $claimlistM->relation(true)->where($w)->order('orderstas asc,regtime desc')->page($page,4)->select();
        if(count($this->claimArr)<4)
        {
            cookie('is_end',1);
        }
        $this->display();
    }
    //认购列表
    public function index2()
    {
        cookie('is_end',0);
        $this->display();
    }

    //认领列表ajax分页
    public function ajaxindex2()
    {
        $page = 1;
        if(isset($_POST['page']))
        {
            $page = intval($_POST['page']);
        }
        $claimlistM = D('Claimlist');
        $w['isdel'] = 0;//实际拥有的数量要大于0
        $w['mid'] = $this->mid;
        $w['kind'] = 2;
        $this->claimArr = $claimlistM->relation(true)->where($w)->order('orderstas asc,regtime desc')->page($page,4)->select();

        if(count($this->claimArr)<4)
        {
            cookie('is_end',1);
        }
        $this->display();
    }

    public function sendProperty()
    {
//        $this->display('approve');
    }


}