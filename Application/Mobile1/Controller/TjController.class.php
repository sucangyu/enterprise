<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Date: 2017/10/28 0028
 * Time: 下午 9:54
 */
namespace Mobile\Controller;
class TjController extends CommonController
{
    //代言首页
    public function index()
    {
        $pre_session = C('SESSION_PRE');
        $mid = $_SESSION[$pre_session.'user']['id'];
        $memberArr = $_SESSION[$pre_session.'user'];
        $jurl = U('Index/index');
        $projlistM = M('Projlist');
        $tjlistM = M('Tjlist');

        if(isset($_GET['proj_id'])&&!empty($_GET['proj_id']))
        {
            $proj_id= intval($_GET['proj_id']);
        }elseif(isset($_GET['tmp_id'])&&!empty($_GET['tmp_id']))
        {
            $tmp_id = intval($_GET['tmp_id']);
            $tjArr = $tjlistM->find($tmp_id);
            if(empty($tjArr)||$tjArr['is_del']==1)
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('给您推荐的代言链接已删除,请重新确认');window.location.href='".$jurl."';</script>";
                exit;
            }
            $proj_id = intval($tjArr['proj_id']);
        }else
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('非法操作');window.location.href='".$jurl."';</script>";
            exit;
        }

        $projArr = $projlistM->find($proj_id);
        if(empty($projArr)||$projArr['isapprove']!=2)
        {//项目要求正常在售
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您代言的项目目前处于关闭状态,请关注我们的公众号会及时更新开放时间，谢谢您的支持');window.location.href='".$jurl."';</script>";
            exit;
        }

        if(isset($_GET['proj_id'])&&!empty($_GET['proj_id']))
        {
            $w['mid'] = $mid;
            $w['proj_id'] = $proj_id;
            $w['is_del'] = 0;

            $tjArr = $tjlistM->where($w)->find();
            if(!empty($tjArr))
            {
                $this->memberArr = $memberArr;
                $this->tjlistArr = $tjArr;
                $this->projArr = $projArr;
                $this->tmp_id = $tjArr['id'];
                $this->display('tmp'.$tjArr['tmp_id']);
            }else
            {
                $jurl = U('Tj/choiceTmp',array('proj_id'=>$proj_id));
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('请先选择您喜欢的代言模板');window.location.href='".$jurl."';</script>";
                exit;
            }
        }elseif(isset($_GET['tmp_id'])&&!empty($_GET['tmp_id']))
        {//通过其他分享代言进入的则更新父id
            $o_mid = $tjArr['mid'];
            if($o_mid)
            {
                $memberArr = M('Member')->find($o_mid);
                if($o_mid!=$mid&&$_SESSION[$pre_session.'user']['pid']==0&&!empty($memberArr))
                {
                    M('Member')->where('id='.$mid)->setField('pid',$o_mid);
                }
            }
            $this->memberArr = $memberArr;
            $this->tjlistArr = $tjArr;
            $this->projArr = $projArr;
            $this->tmp_id = $tjArr['id'];
            $this->display('tmp'.$tjArr['tmp_id']);
        }
    }

    //模板选择页
    public function choiceTmp()
    {
        $pre_session = C('SESSION_PRE');
        $this->memberArr = $_SESSION[$pre_session.'user'];
        $jurl = U('Index/index');

        if(!isset($_REQUEST['proj_id'])||empty($_REQUEST['proj_id']))
        {//项目id不能为空
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误,请重试');window.location.href='".$jurl."';</script>";
            exit;
        }

        $proj_id= intval($_REQUEST['proj_id']);
        $projlistM = M('Projlist');
        $projArr = $projlistM->find($proj_id);
        if(empty($projArr)||$projArr['isapprove']!=2)
        {//项目要求正常在售
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您代言的项目目前处于关闭状态,请关注我们的公众号会及时更新开放时间，谢谢您的支持');window.location.href='".$jurl."';</script>";
            exit;
        }

        $tmp_id = 0;
        if(isset($_REQUEST['tmp_id'])&&!empty($_REQUEST['tmp_id']))
        {
            $tmp_id = intval($_REQUEST['tmp_type']);
        }

        $this->tmp_id = $tmp_id;
        $this->assign('projArr',$projArr);
        $this->display();
    }

    //ajax不刷新变更模板
    public function ajax_tmp()
    {
        $pre_session = C('SESSION_PRE');
        $uid = $_SESSION[$pre_session.'user']['id'];
        $tmp_type = intval($_POST['type']);
        $tjArr['tmp_id'] = $tmp_type;//模板id
        $proj_id = intval($_POST['id']);//项目id
        $memberM = M('Member');
        $projlistM = M('Projlist');
        $memberArr = $memberM->find($uid);
        $projArr = $projlistM->find($proj_id);
        if(empty($projArr)||$projArr['isapprove']!=2)
        {//项目要求正常在售
            $jurl = U('Index/index');
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您代言的项目目前处于关闭状态,请关注我们的公众号会及时更新开放时间，谢谢您的支持');window.location.href='".$jurl."';</script>";
            exit;
        }
        $this->assign('memberArr', $memberArr);
        $this->assign('projArr', $projArr);
        $this->display('tmp'.$tmp_type);
    }

    public function doChoice()
    {
        $pre_session = C('SESSION_PRE');
        $uid = $_SESSION[$pre_session.'user']['id'];
        $jurl = U('Index/index');
        if(!isset($_GET['tmp_id'])||empty($_GET['tmp_id'])||!isset($_GET['proj_id'])||empty($_GET['proj_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误');window.location.href='".$jurl."';</script>";
            exit;
        }
        $proj_id = intval($_GET['proj_id']);
        $tmp_id = intval($_GET['tmp_id']);
        if($tmp_id!=1 && $tmp_id!=2)
        {
            $tmp_id = 1;
        }

        $tjlistM = M('Tjlist');
        $tjlistArr = $tjlistM->where(array('mid' =>$uid,'proj_id'=>$proj_id,'isdel'=>0))->find();
        if(!empty($tjlistArr))
        {
            $jurl = U('Tj/index',array('proj_id'=>$proj_id));
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('不能重复代言同一项目');window.location.href='".$jurl."';</script>";
            exit;
        }

        if($uid&&$proj_id&&$tmp_id)
        {
            if ($tjlistM->add(array('mid' =>$uid,'proj_id'=>$proj_id,'tmp_id'=>$tmp_id,'regtime'=>time())))
            {
                $jurl = U('Tj/index',array('proj_id'=>$proj_id));
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('代言成功');window.location.href='".$jurl."';</script>";
                exit;
            }else{
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('代言失败');history.back();</script>";
                exit;
            }
        }else
        {
            $jurl = U('Tj/index',array('proj_id'=>$proj_id));
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>window.location.href='".$jurl."';</script>";
            exit;
        }
    }
}