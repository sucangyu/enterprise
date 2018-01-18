<?php
/**
 * Created by PhpStorm.
 * User: Huyh
 * Date: 2017/10/30 0030
 * Time: 下午 4:09
 */
namespace Mobile\Controller;
class AddressController extends CommonController
{
    public $pre_session;
    public $mid;
    public function _initialize()
    {
        parent::_initialize();
        $this->pre_session = C('SESSION_PRE');
        $this->mid = $_SESSION[$this->pre_session.'user']['id']; //用户id
    }
    //地址列表
    public function addressList()
    {
        $member_addressM = M('Member_address');
        $addressList = $member_addressM->where('is_del=0 and mid='.$this->mid)->order('id DESC')->select();
        $this->addressList = $addressList;
        $this->display();
    }

    //添加地址
    public function addAddress()
    {
        $regionM = M('Region');
        $list = $regionM->where(array('parent_id' =>0 ))->select();
        $this->assign('tree', json_encode($list));
        $this->display();
    }

    //ajax地址联动
    public function ajaxCityList(){
        $regionM = M('Region');
        if(!$_POST||empty($_POST['lid']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('获取地区参数错误');window.location.href='".U('User/index')."';</script>";
            exit;
        }
        $list = $regionM->where(array('parent_id' =>$_POST['lid']))->select();
        exit(json_encode($list));
    }

    //添加地址方法
    public function doAddaddress()
    {
        $jurl = U('Address/addressList');
        if(!IS_POST)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('非法访问');window.location.href='".$jurl."';</script>";
            exit;
        }
        if(!isset($_POST['consignee'])||empty($_POST['consignee']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('请填写收货人姓名');history.back();</script>";
            exit;
        }

        if(!isset($_POST['phone'])||empty($_POST['phone']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('请填写联系方式');history.back();</script>";
            exit;
        }

        if(!isset($_POST['detailed'])||empty($_POST['detailed']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('请填写具体地址');history.back();</script>";
            exit;
        }

        $addDate['mid'] = $this->mid;
        $addDate['regtime'] = time();
        $addDate['consignee'] = $_POST['consignee'];
        $addDate['mobile'] = $_POST['phone'];
        if(isset($_POST['pre'])&&!empty($_POST['pre']))
        {
            $addDate['province'] = $_POST['pre'];
        }
        if(isset($_POST['city'])&&!empty($_POST['city']))
        {
            $addDate['city'] = $_POST['city'];
        }
        if(isset($_POST['area'])&&!empty($_POST['area']))
        {
            $addDate['district'] = $_POST['area'];
        }
        $addDate['address'] = $_POST['detailed'];
        if(isset($_POST['default'])&&!empty($_POST['default']))
        {
            $addDate['is_default'] = $_POST['default'];
        }

        $member_addressM = M('Member_address');
        $res1 = 1;
        $res2 = 1;
        M()->startTrans();
        $memberAddressArr = $member_addressM->where('is_del=0 and is_default=1 and mid='.$this->mid)->select();
        if(!empty($memberAddressArr)&&$_POST['default']==1)
        {
            $res1 = $member_addressM->where('is_del=0 and is_default=1 and mid='.$this->mid)->setField('is_default',0);
        }

        if(empty($memberAddressArr))
        {
            $addDate['is_default'] = 1;
        }
        $res2 = $member_addressM->add($addDate);

        if($res1&&$res2)
        {
            M()->commit();
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('新地址添加成功');location.href='".$jurl."'</script>";
            exit;
        }else
        {
            M()->rollback();
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('地址添加失败，请重试');location.href='".$jurl."'</script>";
            exit;
        }
    }

    //修改默认地址方法
    public function ajaxDefaultCity()
    {
        if(!$_POST||empty($_POST['lid']))
        {
            exit($this->ajaxReturn(array('msg'=>'非法访问','status'=>0,'jurl'=>U('Address/addressList'))));
        }
        $lid = intval($_POST['lid']);

        $member_addressM = M('Member_address');
        $memberAddressArr = $member_addressM->where('is_del=0 and is_default=1 and mid='.$this->mid)->select();

        M()->startTrans();
        $res1 = 1;
        $res2 = 1;

        if(!empty($memberAddressArr))
        {
            $res1 = $member_addressM->where('is_del=0 and is_default=1 and mid='.$this->mid)->setField('is_default',0);
        }

        $res2 = $member_addressM->where('id='.$lid.' and is_del=0 and mid='.$this->mid)->setField('is_default',1);


        if($res1 && $res2)
        {
            M()->commit();
            exit($this->ajaxReturn(array('msg'=>'默认地址设置成功','status'=>1,'jurl'=>U('Address/addressList'))));

        }else
        {
            M()->rollback();
            exit($this->ajaxReturn(array('msg'=>'默认地址设置失败','status'=>0,'jurl'=>U('Address/addressList'))));
        }

    }

    //编辑地址
    public function editAddress()
    {
        $jurl = U('Address/addressList');
        if(!isset($_REQUEST['aid'])||empty($_REQUEST['aid']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('地址参数丢失');window.location.href='".$jurl."';</script>";
            exit;
        }
        $member_addressM = M('Member_address');
        $aid = intval($_REQUEST['aid']);
        $addressArr = $member_addressM->find($aid);
        if(empty($addressArr)||$addressArr['is_del']==1)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您要修改的地址不存在');window.location.href='".$jurl."';</script>";
            exit;
        }
        if(IS_POST)
        {
            if(!isset($_POST['consignee'])||empty($_POST['consignee']))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('请填写收货人姓名');history.back();</script>";
                exit;
            }

            if(!isset($_POST['phone'])||empty($_POST['phone']))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('请填写联系方式');history.back();</script>";
                exit;
            }

            if(!isset($_POST['detailed'])||empty($_POST['detailed']))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('请填写具体地址');history.back();</script>";
                exit;
            }

            $addDate['mid'] = $this->mid;
            $addDate['consignee'] = $_POST['consignee'];
            $addDate['mobile'] = $_POST['phone'];
            if(isset($_POST['pre'])&&!empty($_POST['pre']))
            {
                $addDate['province'] = $_POST['pre'];
            }
            if(isset($_POST['city'])&&!empty($_POST['city']))
            {
                $addDate['city'] = $_POST['city'];
            }
            if(isset($_POST['area'])&&!empty($_POST['area']))
            {
                $addDate['district'] = $_POST['area'];
            }
            $addDate['address'] = $_POST['detailed'];
            $addDate['regtime'] = time();

            $member_addressM = M('Member_address');
            $res1 = 1;
            $res2 = 1;
            M()->startTrans();
            if($addDate['consignee']!=$addressArr['consignee']||$addDate['mobile']!=$addressArr['mobile']||$addDate['province']!=$addressArr['province']||$addDate['city']!=$addressArr['city']||$addDate['district']!=$addressArr['district']||$addDate['address']!=$addressArr['address'])
            {
                if($addressArr['is_default']==1)
                {
                    $addDate['is_default'] = 1;
                }
                $res2 = $member_addressM->add($addDate);
                $res1 = $member_addressM->where('id='.$aid)->setField('is_del',1);
            }

            if($res1&&$res2)
            {
                M()->commit();
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('地址修改成功');location.href='".$jurl."'</script>";
                exit;
            }else
            {
                M()->rollback();
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('地址修改失败，请重试');location.href='".$jurl."'</script>";
                exit;
            }

        }else
        {
            $this->tree = json_encode(M('Region')->where(array('parent_id' =>0 ))->select());
            $this->assign('addressArr',$addressArr);
            $this->display();
        }

    }

    //ajax删除地址
    public function delAddress()
    {
        if(!$_POST||empty($_POST['aid']))
        {
            exit($this->ajaxReturn(array('msg'=>'地址参数丢失','status'=>0,'jurl'=>U('Address/addressList'))));
        }
        $aid = intval($_POST['aid']);
        $member_addressM = M('Member_address');
        $memberAddressArr = $member_addressM->find($aid);
        if(empty($memberAddressArr)||$memberAddressArr['is_del']==1)
        {
            exit($this->ajaxReturn(array('msg'=>'您的地址不存在或已删除','status'=>0,'jurl'=>U('Address/addressList'))));
        }
        if($member_addressM->where('id='.$aid)->setField('is_del',1))
        {
            if($memberAddressArr['is_default']==1)
            {//删除的若是默认地址，则系统自动选择一个默认地址
                $tempArr = $member_addressM->where('is_del=0 and mid='.$this->mid)->order('is_default desc,regtime desc')->limit(1)->find();
                if(!empty($tempArr)&&$tempArr['is_default']==0)
                {
                    $member_addressM->where('id='.$tempArr['id'])->setField('is_default',1);
                }
            }
            exit($this->ajaxReturn(array('msg'=>'地址删除成功','status'=>1,'jurl'=>U('Address/addressList'))));
        }else
        {
            exit($this->ajaxReturn(array('msg'=>'地址删除失败','status'=>0,'jurl'=>U('Address/addressList'))));
        }

    }
}