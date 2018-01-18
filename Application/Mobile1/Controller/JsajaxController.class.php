<?php
/**
 * Created by PhpStorm.
 * User: Huyh
 * Date: 2017/11/2 0002
 * Time: 上午 9:41
 */
namespace Mobile\Controller;
use Think\Controller;
class JsajaxController extends Controller
{
    //会员校验
    public function memberCheck()
    {
        $data['stsc'] = 0;
        $data['msg'] = '非法请求';
        $data['url'] = '';

        if(!isset($_POST['ajkd'])||empty($_POST['ajkd'])||!isset($_POST['member_number'])||empty($_POST['member_number']))
        {
            $this->ajaxReturn($data);
        }
        $ajakd = intval($_POST['ajkd']);
        if(!in_array($ajakd,array(1,2)))
        {
            $this->ajaxReturn($data);
        }

        $member_num = intval($_POST['member_number']);
        $initNum = C('MEMBER_NUM');
        $mid = $member_num-$initNum;
        if($mid<=0)
        {
            $data['msg'] = '爱心大使编号格式错误:'.$mid;
            $this->ajaxReturn($data);
        }

        $session_pre = C('SESSION_PRE');
        $selfMid = $_SESSION[$session_pre.'user']['id'];

        if($selfMid==$mid)
        {
            $data['msg'] = '不能给自己赠送资源';
            $this->ajaxReturn($data);
        }

        $memberArr = M('Member')->field('username,nickname,userphone')->find($mid);
        if(empty($memberArr))
        {
            $data['msg'] = '爱心大使不存在';
            $this->ajaxReturn($data);
        }else
        {
            $data['stsc'] = 1;
            $data['msg'] = '爱心大使昵称:'.$memberArr['nickname'];
            $this->ajaxReturn($data);
        }
    }

    //获取收成计算1
    public function getClaim()
    {
        $data['stsc'] = 0;
        $data['msg'] = '非法请求';
        $data['url'] = '';
        $data['maxnum'] = 0;//最大可领取数量
        $data['sendmoney'] = 0;//总运费
        $data['omoney'] = 0;//总加工费
        if(!isset($_POST['property_id'])||!isset($_POST['goods_id'])||empty($_POST['property_id'])||empty($_POST['goods_id']))
        {
            $this->ajaxReturn($data);
        }

        $property_id = $_POST['property_id'];
        $goods_id = $_POST['goods_id'];
        $batchnum = strtotime(date('Y').'-01-01');//当前期号

        $goodsArr = M('Goods')->find($goods_id);
        if(empty($goodsArr)||$goodsArr['kind']!=2)
        {
            $data['stsc'] = 0;
            $data['msg'] = '您领取的收成不存在';
            $data['url'] = '';
            $this->ajaxReturn($data);
        }
//        $batchnum = $goodsArr['batchnum'];
        $w['id'] = array('in',$property_id);
        $propertyArr = M('Property')->where($w)->select();
        unset($w);
        if(empty($propertyArr))
        {
            $data['stsc'] = 0;
            $data['msg'] = '您没有可领取的收成';
            $data['url'] = '';
            $this->ajaxReturn($data);
        }

        $propertytakeM = M('Propertytake');
        $session_pre = C('SESSION_PRE');
        $mid = $_SESSION[$session_pre.'user']['id'];
        //统计总的可领取数量以及已认领的数量
        foreach ($propertyArr as $key=>$val)
        {
            $w['mid'] = $mid;
            $w['property_id'] = $val['id'];
            $w['batchnum'] = $batchnum;
            $temp_num = $propertytakeM->where($w)->sum('nums');
            if(!empty($temp_num))
            {
                $temp_num = intval($temp_num);
            }else
            {
                $temp_num = 0;
            }
            $data['maxnum'] = $data['maxnum']+$val['totaltree']-$temp_num;
        }

        $temp_sendMoney1 = fmod($data['maxnum'],$goodsArr['sendmax']);
        if(empty($temp_sendMoney1))
        {
            $temp_sendMoney1 = 0;
        }
        $temp_sendMoney2 = floor($data['maxnum']/$goodsArr['sendmax']);
        if($temp_sendMoney1 != 0)
        {
            $temp_sendMoney2 = $temp_sendMoney2+1;
        }

        if($goodsArr['minpropertynum']!=0)
        {
            $data['maxnum'] = floor($data['maxnum']/$goodsArr['minpropertynum']);
        }

        if($goodsArr['sendmoney']!=0&&$goodsArr['sendmax']!=0)
        {
            $data['sendmoney'] = $temp_sendMoney2*$goodsArr['sendmoney'];//总自付运费
        }

        if($goodsArr['sendtomoney']!=0&&$goodsArr['sendmax']!=0)
        {
            $data['sendtomoney'] = $temp_sendMoney2*$goodsArr['sendtomoney'];//总到付运费
        }

        $data['omoney'] = $data['maxnum']*$goodsArr['omoney'];//总加工费
        $data['stsc'] = 1;
        $data['msg'] = '请求成功';
        $this->ajaxReturn($data);
    }
    //获取收成计算2
    public function getClaim2()
    {
        $data['stsc'] = 0;
        $data['msg'] = '非法请求';
        $data['url'] = '';
        $data['maxnum'] = 0;//最大可领取数量
        $data['sendmoney'] = 0;//总运费
        $data['omoney'] = 0;//总加工费
        if(!isset($_POST['property_id'])||!isset($_POST['goods_id'])||empty($_POST['property_id'])||empty($_POST['goods_id']))
        {
            $this->ajaxReturn($data);
        }

        $property_id = $_POST['property_id'];
        $goods_id = $_POST['goods_id'];
//        $batchnum = strtotime(date('Y').'-01-01');//当前期号

        $goodsArr = M('Goods')->find($goods_id);
        if(empty($goodsArr)||$goodsArr['kind']!=2)
        {
            $data['stsc'] = 0;
            $data['msg'] = '您领取的收成不存在';
            $data['url'] = '';
            $this->ajaxReturn($data);
        }
        $batchnum = $goodsArr['batchnum'];
        $w['id'] = array('in',$property_id);
        $propertyArr = M('Property')->where($w)->select();
        unset($w);
        if(empty($propertyArr))
        {
            $data['stsc'] = 0;
            $data['msg'] = '您没有可领取的收成';
            $data['url'] = '';
            $this->ajaxReturn($data);
        }

        $propertytakeM = M('Propertytake');
        $session_pre = C('SESSION_PRE');
        $mid = $_SESSION[$session_pre.'user']['id'];
        //统计总的可领取数量以及已认领的数量
        foreach ($propertyArr as $key=>$val)
        {
            $w['mid'] = $mid;
            $w['property_id'] = $val['id'];
            $w['batchnum'] = $batchnum;
            $temp_num = $propertytakeM->where($w)->sum('nums');
            if(!empty($temp_num))
            {
                $temp_num = intval($temp_num);
            }else
            {
                $temp_num = 0;
            }
            $data['maxnum'] = $data['maxnum']+$val['totaltree']-$temp_num;
        }
        $data['maxnum'] = floor($data['maxnum']/$goodsArr['minpropertynum']);
        $data['sendmoney'] = ceil($data['maxnum']/$goodsArr['sendmax'])*$goodsArr['sendmoney'];//总自付运费
        $data['sendtomoney'] = ceil($data['maxnum']/$goodsArr['sendmax'])*$goodsArr['sendtomoney'];//总到付运费
        $data['omoney'] = $data['maxnum']*$goodsArr['omoney'];//总加工费
        $data['stsc'] = 1;
        $data['msg'] = '请求成功';
        $this->ajaxReturn($data);
    }
}