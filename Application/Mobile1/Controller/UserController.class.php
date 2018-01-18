<?php
/**
 * Created by PhpStorm.
 * User: Huyh
 * Date: 2017/11/20 0020
 * Time: 上午 10:26
 */
namespace Mobile\Controller;
class UserController extends CommonController
{
    public $pre_session;
    public $mid;
    public function _initialize()
    {
        parent::_initialize();
        $this->pre_session = C('SESSION_PRE');
        $this->mid = session($this->pre_session.'user_id');
    }
    //我的庄园首页
    public function index()
    {
        $this->memberArr = session($this->pre_session.'user');
        $this->display();
    }
    //爱心列表（捐助打赏列表）
    public function supportList()
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

    //ajax爱心列表
    public function ajaxSupportList()
    {
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
        $supportsM = D('Supports');
        $w['paystas'] = $pays;
        if($pays == 2 )
        {
            unset($w['paystas']);
        }
        $w['mid'] = $this->mid;
        $w['isdel'] = 0;
        $this->supportsArr = $supportsM->relation(true)->where($w)->order('paystas asc ,regtime desc')->page($page,4)->select();
        if(count($this->supportsArr)<4)
        {//前端没有更多提示用
            cookie('is_end',1);
        }
        $this->pays = $pays;
        $this->display();
    }

    //查看代付二维码
    public function show_ewm()
    {
        $jurl = U('User/supportlist');
        if(!isset($_REQUEST['sid']) || empty($_REQUEST['sid']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('请到我的庄园-捐助列表查看待支付二维码');window.location.href='".$jurl."';</script>";
            exit;
        }

        $this->sid = $_REQUEST['sid'];
        $pre_session  = C('SESSION_PRE');
        $this->memberArr = $_SESSION[$pre_session.'user'];
        $this->display();
    }
    //个人信息
    public function memberInfo()
    {
        $memberArr = session($this->pre_session.'user');
        if(IS_POST)
        {
            $username = I("post.username");
            $userphone = I("post.userphone");

            if($username != $memberArr['username'] || $userphone != $memberArr['userphone'])
            {
                $jurl = U('User/memberInfo');
                $msg = '修改失败';
                if(M('Member')->where(array('id' =>$this->mid ))->save(array('username' =>$username,'userphone' =>$userphone)))
                {
                    $msg = '修改成功';
                }
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('个人资料信息$msg');window.location.href='".$jurl."';</script>";
                exit;
            }
        }
        $this->display();
    }

    //爱心排行榜
    public function aixin()
    {
        $this->aixinArr = M('Member')->order('coins desc')->limit(10)->select();
        $this->display();
    }

    //资产--认领记录（爱心记录）
    public function claimList()
    {
        cookie('is_end',0);
        $this->display();
    }

    //资产--认领记录（爱心记录）
    public function ajaxClaimList()
    {
        $page = 1;
        if(isset($_POST['page']))
        {
            $page = intval($_POST['page']);
        }
        $lovelistM = M('Lovelist');
        $w['mid'] = $this->mid;
        $this->loveArr = $lovelistM->where($w)->order('regtime desc')->page($page,10)->select();
        if(count($this->loveArr)<=10)
        {
            cookie('is_end',1);
        }
        $this->display();
    }
}