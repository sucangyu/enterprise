<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Date: 2017/10/28 0028
 * Time: 上午 11:51
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
        $this->mid = $_SESSION[$this->pre_session.'user']['id']; //用户id
//        $this->mid = 1; //用户id
    }
    //我的庄园首页
    public function index()
    {
        session_start();
//        $_SESSION['jssm_user'][id]=1;
        $_SESSION[$this->pre_session.'user']['id']=1;
//        $_SESSION['id']=1;
        $user=M('Member')->find($this->mid);
//        var_dump($_SESSION);die;

        $this->assign('user',$user);
        $this->display();
    }

    //捐助列表（支付列表）
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
        $pre_session  = C('SESSION_PRE');
        $this->memberArr = $_SESSION[$pre_session.'user'];
        $this->display();
    }

    //ajax支持列表
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
        $memberM = M('Member');
//        echo $this->mid;
        $memberArr = $memberM->find($this->mid);
//        var_dump($memberArr);
        if(IS_POST)
        {
//            var_dump($_POST);
            $username = I("post.username");
            $userphone = I("post.userphone");

            if($username != $memberArr['username'] || $userphone != $memberArr['userphone'])
            {
                $jurl = U('User/index');
                $msg = '修改失败';
                if($memberM->where(array('id' =>$this->mid ))->save(array('username' =>$username,'userphone' =>$userphone)))
                {
                    $msg = '修改成功';
                }
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('个人资料信息$msg');window.location.href='".$jurl."';</script>";
                exit;
            }
        }
        $this->assign('memberArr',$memberArr);
        $this->display();
    }
    public function statslist(){
        $this->display();
    }
    public function property(){
//        $mid=$_SESSION[]
        $proj = M('Projlist')->where('isapprove = 2')->order('regtime desc')->select();
        $proj_id='';
        for($i=0;$i<count($proj);$i++){
            $proj_id=$proj_id."'".$proj[$i]['id']."',";
        }
        if(!empty($proj_id)){
            $proj_id=substr($proj_id, 0, -1);
        }else{

        }
        echo($proj_id);
        $nian='20'.date('y',time());
        $projc = M('Goods')->where('kind = 2 and stats=2 and proj_id in ('.$proj_id.')')->select();
        foreach($projc as $k=>$v){
//            $sql='select sum ('
            $batchnum = M('Property')->where('batchnum < '.$nian.' and mid = '.$this->mid)->sum('totaltree');
            $projc[$k]['totaltree']=$batchnum;
//            $batchnum
        }
        $propertylist = M('propertylist')->where('mid='.$this->mid)->select();
        $user=M('Member')->find($this->mid);
        echo $projc[0][totaltree]-$projc[0][minpropertynum];
//        echo ;
//        var_dump($projc);
        $this->assign('propertylist',$propertylist);
        $this->assign('user',$user);
        $this->assign('projc',$projc);
        $this->display();
    }
    public function myget(){
//        $this->mid=1;
        $property=M('Property')->where('mid='.$this->mid)->select();

        foreach($property as $k=>$v){
            $pro[$v['batchnum']]=$v['totaltree'];
            $num+=$v['totaltree'];
        }
//        echo $num;
//        var_dump($pro);
        $this->assign('pro',$pro);
        $this->assign('num',$num);
        $this->display();
    }
    public function orderlist(){
        $this->display();
    }
    public function renling(){
//        var_dump($_POST);
        $gid=$_POST['id'];
        $projc=M('Goods')->find($gid);
        $nian='20'.date('y',time());
        $totaltree=M('Property')->where('batchnum<'.$nian.' and mid='.$this->mid)->select();
        foreach($totaltree as $v){
            $Property[$v[batchnum]]=$v[totaltree];
        }
//        var_dump($Property);
//        var_dump($projc);
        $ress=M('member_address')->where('is_del=0 and is_default=1 and mid='.$this->mid)->select();
        if(empty($ress)){
            $this->assign('ress','1');
        }else{
            $this->assign('ress',$ress);
        }
        $this->assign('goods',$projc);
        var_dump($projc);
        $this->display();
    }
    public function aixin(){

        $aizhi=M('Member')->where('id='.$this->mid)->select();
//        var_dump($aizhi);

//        var_dump($aixin);die;

        $this->assign('zhi',$aizhi);
        $this->display();
    }
    public function ajaxaixin(){
        $page=I('page',1);
        $aixin=M('Supports')->where('isdel = 0 and mid = '.$this->mid)->order('regtime desc')->page($page,6)->select();
        $this->assign('aixin',$aixin);
        $this->display();
    }
//    public function wxpay(){
////        var_dump($_GET);die;
//    }
}