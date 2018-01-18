<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27 0027
 * Time: 上午 10:03
 */
namespace Home\Controller;
use Think\Controller;
use Home\Logic;

class CommonController extends Controller
{
    public $user = array();
    public $user_id = 0;
    /*
     * 初始化操作
     */
    public function _initialize()
    {
        $enprM = M('Enpr_info');
        $enprArr = $enprM->find();
        $ress = $this->memaddress($enprArr['province'],$enprArr['city'],$enprArr['district']);
        // var_dump($ress);
        // die;
        $this->assign('ress',$ress);
        $this->assign('enprArr',$enprArr);
    }

    //根据id返回地址名称
    public function memaddress($province,$city,$district)
    {
        $model_region = M('Region');
        $temp_arr = array();
        if(!empty($province))
            $temp_arr['province'] = $model_region->where('id='.$province)->getField('name');
        if(!empty($city))
            $temp_arr['city'] = $model_region->where('id='.$city)->getField('name');
        if(!empty($district))
            $temp_arr['district'] = $model_region->where('id='.$district)->getField('name');
        return $temp_arr;
    }

}