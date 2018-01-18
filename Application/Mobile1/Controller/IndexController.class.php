<?php
namespace Mobile\Controller;
use Think\Controller;
class IndexController extends CommonController
{
    //首页
    public function index()
    {
        $pres_session = C('SESSION_PRE');
        if(isset($_GET['smid'])&&!empty($_GET['smid']))
        {
            $_SESSION[$pres_session.'smid'] = $_GET['smid'];
        }
        //统计打赏人数
        $where['proj_id'] = 1;
        $where['paystas'] = 1;
        $this->supportNum = M('Supports')->where($where)->count();

        //显示最新的10条留言--全部留言显示待定
        unset($where);
        $where['kind'] = 1;
        $where['support_id'] = array('gt',0);
        $where['proj_id'] = 1;
        $where['isdel'] = 0;
        $where['pid'] = 0;
        $this->leaveMsg = D('Commentlist')->relation(true)->where($where)->order('id desc')->limit(10)->select();
        if(!isset($_GET['first'])&&isset($_SESSION[$pres_session.'smid'])&&!empty($_SESSION[$pres_session.'smid']))
        {
            $mid = $_SESSION[$pres_session.'user_id'];
            $smid = $_SESSION[$pres_session.'smid'];
            $shareMember = M('Member')->find($smid);
            if(empty($shareMember))
            {
                $jurl = U('Index/index');
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('您访问的分享页面不存在');window.location.href='".$jurl."';</script>";
                exit;
            }
            $this->shareMember = $shareMember;
            $memberArr = array();
            if($mid)
            {
                $memberArr = M('Member')->find($mid);
            }

            if($mid!=4&&!empty($memberArr)&&$memberArr['pid']==0&&$mid!=$smid&&$shareMember['pid']!=$mid)
            {//更新当前会员的pid
                M('Member')->where('id='.$mid)->setField('pid',$smid);
            }
            $this->display('js_share');
        }else
        {
            if(isset($_SESSION[$pres_session.'smid']))
            {
                unset($_SESSION[$pres_session.'smid']);
            }
            $this->display();
        }
    }
    //项目详情
    public function detail()
    {
        $jurl = U('Index/index');
        if(!isset($_REQUEST['id'])||empty($_REQUEST['id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误');window.location.href='".$jurl."';</script>";
            exit;
        }
        $proj_id = intval($_REQUEST['id']);
        $projArr = M('Projlist')->find($proj_id);
        $this->projArr = $projArr;
        $this->display();
    }

    //我要认领--参数smid存在 则是帮人领取
    public function supports()
    {
        $jurl = U('Index/index');
        if(!isset($_REQUEST['proj_id'])||empty($_REQUEST['proj_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误');window.location.href='".$jurl."';</script>";
            exit;
        }
        $proj_id = intval($_REQUEST['proj_id']);
        $projArr = M('Projlist')->find($proj_id);
        if(empty($projArr)||$projArr['isapprove']!=2)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('认领项目已下架');window.location.href='".$jurl."';</script>";
            exit;
        }
        $this->saleMoney = $projArr['salemoney'];
        $this->proj_id = $projArr['id'];

        /* 帮领判断开始 */
        $smid = 0;
        if(isset($_GET['smid'])&&!empty($_GET['smid']))
        {
            $smid = intval($_GET['smid']);
            $shareMember = M('Member')->find($smid);
            if(empty($shareMember))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('您要帮领的人员不存在');window.location.href='".$jurl."';</script>";
                exit;
            }
        }
        $this->smid = $smid;
        /* 帮领判断结束 */
        $this->display();
    }

    //认领方式选择
    public function claimKind()
    {
        $pre_session = C('SESSION_PRE');
        $jurl = U('Index/index');
        if(!IS_POST||(!isset($_POST['sub1'])&&!isset($_POST['sub2'])))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'> alert('非法访问');window.location.href='".$jurl."';</script>";
            exit;
        }
        if(!isset($_POST['proj_id'])||empty($_POST['proj_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'> alert('认领的项目不存在');window.location.href='".$jurl."';</script>";
            exit;
        }
        $proj_id = intval($_POST['proj_id']);

        $supportsM = M('Supports');
        $projlistM = M('Projlist');
        $projArr = $projlistM->find($proj_id);
        //基本数据验证
        if(empty($projArr)||$projArr['isapprove']!=2)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'> alert('认领的项目已下架');window.location.href='".$jurl."';</script>";
            exit;
        }

        if(!isset($_POST['claimNum'])||empty($_POST['claimNum'])||$_POST['claimNum']<1)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'> alert('认领的数量不能低于1棵');window.location.href='".$jurl."';</script>";
            exit;
        }
        $claimNum = intval($_POST['claimNum']);

        //存数据
        $saveData['kind'] = 4;
        $saveData['proj_id'] = $_POST['proj_id'];
        $saveData['paynum'] = date("ymdHis").rand(1000,9999);
        $saveData['mid'] = $_SESSION[$pre_session.'user']['id'];
        $saveData['dfmid'] = $_SESSION[$pre_session.'user']['id'];
        if(isset($_POST['smid'])&&!empty($_POST['smid']))
        {//若帮领入口进入的  则所属会员id应为需要帮领的id,代领id为当前用户id
            $saveData['mid'] = intval($_POST['smid']);
            $saveData['dlmid'] = $_SESSION[$pre_session.'user']['id'];
        }
        $saveData['treenum'] = $claimNum;
        $saveData['tmoney'] = $claimNum*$projArr['salemoney'];
        $saveData['regtime'] = time();

        if(isset($_POST['sub1']))
        {//自己支付
            $saveData['paykind'] = 1;
            if(!$res1 = $supportsM->add($saveData))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'> alert('服务器请求失败，请重试');window.location.href='".$jurl."';</script>";
                exit;
            }
            $jurl = "http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/wxpay";
            session($pre_session.'support_id',$res1);
            unset($_POST);
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>parent.location.href='".$jurl."';</script>";
            exit;
        }else
        {//他人代付
            require_once './phpqrcode.php';
            $saveData['paykind'] = 2;
            if(!$res1 = $supportsM->add($saveData))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('服务器异常,请重新选择支持方式');parent.location.href='".$jurl."';</script>";
                exit;
            }

//            $ewm = 'http://api.yiwob.com/Mobile/Wxpay/dfpay?support_id='.$res1;
//            $ewm = 'http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Index/dfpay?support_id='.$res1;
            $ewm = 'http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/dfpay?support_id='.$res1;

            // 生成的文件名
            $filename = './Public/support_ewm/js_support'.$res1.'.png';
            // 纠错级别：L、M、Q、H
            $errorCorrectionLevel = 'L';
            // 点的大小：1到10
            $matrixPointSize = 4;
            //创建一个二维码文件
            \QRcode::png($ewm, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
            $jurl = U('User/show_ewm',array('sid'=>$res1));
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('支付二维码生成成功,请将该图发送给您的好友,请他代付');parent.location.href='".$jurl."';</script>";
            exit;
        }
    }

    //他人代付入口
    public function dfpay()
    {
        $pre_session = C('SESSION_PRE');
        $jurl = U('Index/index');
        if(!isset($_REQUEST['support_id'])||empty($_REQUEST['support_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误');window.location.href='".$jurl."';</script>";
            exit;
        }
        $support_id = $_REQUEST['support_id'];
        $supportM = D('Supports');
        $supportArr = $supportM->relation(true)->find($support_id);
        if (empty($supportArr))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您要支付的项目不存在');window.location.href='".$jurl."';</script>";
            exit;
        }

        if ($supportArr['paystas']==1)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您要支付的项目已支付,不能重复支付');window.location.href='".$jurl."';</script>";
            exit;
        }

        if(IS_POST)
        {
            if((!isset($_POST['support_id'])||empty($_POST['support_id'])))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'> alert('非法请求');window.location.href='".$jurl."';</script>";
                exit;
            }
            $jurl = "http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/wxpay";
            session($pre_session.'support_id',$_POST['support_id']);
            session($pre_session.'has_df_id',$_SESSION[$pre_session.'user']['id']);
            unset($_POST);
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>window.location.href='".$jurl."';</script>";
            exit;
        }else
        {
            //代付申请人信息
            $dfMember  = M('Member')->find($supportArr['mid']);
            if($supportArr['dlmid'])
            {//以dlmid存在的人优先显示
                $dfMember  = M('Member')->find($supportArr['dlmid']);
            }
            if(empty($dfMember))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('代付申请人信息不存在,请确认后重试');window.location.href='".$jurl."';</script>";
                exit;
            }

            if($supportArr['mid']==$_SESSION[$pre_session.'user']['id'])
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'> alert('您正在给您自己代付');</script>";
            }
            $this->dfMember = $dfMember;

            if(!isset($supportArr['Projlist']['projimages'])&&!empty($supportArr['Projlist']['projimages']))
            {
                $supportArr['Projlist']['projimages'] = json_decode($supportArr['Projlist']['projimages']);
                $supportArr['Projlist']['projimages'] = $supportArr['Projlist']['projimages'][0];
            }
            $this->supportArr = $supportArr;
            $this->display();
        }
    }

    //打赏
    public function rewards()
    {
        $pre_session = C('SESSION_PRE');
        $jurl = U('Index/index');
        if(!IS_POST||!isset($_POST['rewardMoney'])||empty($_POST['rewardMoney'])||$_POST['rewardMoney']<=0)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'> alert('打赏金额不能少于0');window.location.href='".$jurl."';</script>";
            exit;
        }
        if(!isset($_POST['proj_id'])||empty($_POST['proj_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'> alert('打赏的项目不存在');window.location.href='".$jurl."';</script>";
            exit;
        }
        $proj_id = intval($_POST['proj_id']);

        $supportsM = M('Supports');
        $projlistM = M('Projlist');
        $projArr = $projlistM->find($proj_id);
        //基本数据验证
        if(empty($projArr)||$projArr['isapprove']!=2)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'> alert('打赏的项目已下架');window.location.href='".$jurl."';</script>";
            exit;
        }

        $rewardMoney = $_POST['rewardMoney'];

        //存数据
        $saveData['kind'] = 3;
        $saveData['proj_id'] = $proj_id;
        $saveData['paynum'] = date("ymdHis").rand(1000,9999);
        $saveData['mid'] = $_SESSION[$pre_session.'user']['id'];
        $saveData['tmoney'] = $rewardMoney;
        $saveData['paykind'] = 1;
        $saveData['regtime'] = time();

        if(!$res1 = $supportsM->add($saveData))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'> alert('服务器请求失败，请重试');window.location.href='".$jurl."';</script>";
            exit;
        }
        $jurl = "http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/rewardPay";
        session($pre_session.'support_id',$res1);
        unset($_POST);
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo "<script type='text/javascript'>parent.location.href='".$jurl."';</script>";
        exit;
    }
    //支付成功页面显示
    public function payOk()
    {
        $this->display();
    }

    //免责条款
    public function zcxy()
    {
        $this->noticeArr = M('Notices')->find(1);
        $this->display();
    }

    //酒标签中转页面
    public function wineInfo()
    {
        $this->display();
    }

}