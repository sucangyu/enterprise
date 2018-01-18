<?php
/**
 * Created by PhpStorm.
 * User: Huyh
 * Use: 公共函数
 * Date: 2017/3/17
 * Time: 14:23
 */
// 定义一个函数getIP() 客户端IP，
function getIP(){
    if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if(getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if(getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else $ip = "Unknow";
    return $ip;
}

/**
 * 获取缓存或者更新缓存
 * @param string $config_key 缓存文件名称
 * @param array $data 缓存数据  array('k1'=>'v1','k2'=>'v3')
 * @return array or string or bool
 */
function dsCache($config_key,$data = array()){
    $param = explode('.', $config_key);
    if(empty($data)){
        //如$config_key=shop_info则获取网站信息数组
        //如$config_key=shop_info.logo则获取网站logo字符串
        $config = F($param[0],'',TEMP_PATH);//直接获取缓存文件
        if(empty($config)){
            //缓存文件不存在就读取数据库
            $res = D('config')->where("inc_type='$param[0]'")->select();
            if($res){
                foreach($res as $k=>$val){
                    $config[$val['name']] = $val['value'];
                }
                F($param[0],$config,TEMP_PATH);
            }
        }
        if(count($param)>1){
            return $config[$param[1]];
        }else{
            return $config;
        }
    }else{
        //更新缓存
        $result =  D('config')->where("inc_type='$param[0]'")->select();
        if($result){
            foreach($result as $val){
                $temp[$val['name']] = $val['value'];
            }
            foreach ($data as $k=>$v){
                $newArr = array('name'=>$k,'value'=>trim($v),'inc_type'=>$param[0]);
                if(!isset($temp[$k])){
                    M('config')->add($newArr);//新key数据插入数据库
                }else{
                    if($v!=$temp[$k])
                        M('config')->where("name='$k'")->save($newArr);//缓存key存在且值有变更新此项
                }
            }
            //更新后的数据库记录
            $newRes = D('config')->where("inc_type='$param[0]'")->select();
            foreach ($newRes as $rs){
                $newData[$rs['name']] = $rs['value'];
            }
        }else{
            foreach($data as $k=>$v){
                $newArr[] = array('name'=>$k,'value'=>trim($v),'inc_type'=>$param[0]);
            }
            M('config')->addAll($newArr);
            $newData = $data;
        }
        return F($param[0],$newData,TEMP_PATH);
    }
}

/**
 * 判断当前访问的用户是  PC端  还是 手机端  返回true 为手机端  false 为PC 端
 * @return boolean
 */
/**
　　* 是否移动端访问访问
　　*
　　* @return bool
　　*/
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            return true;
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}

/* 根据标识获取配置信息或更新配置信息 */
function getConfig($inc_type)
{
    $config = array();
    $res = M('Config')->where("inc_type='$inc_type'")->select();
    if($res)
    {
        foreach($res as $k=>$val)
        {
            $config[$val['name']] = $val['value'];
        }
    }
    return $config;
}

/*
 * 获取推荐人信息
 * */

function getPidMember($pid)
{
    return M('Member')->where('id='.$pid)->getField('nickname');
}
/*
 * 获取回复信息
 * */

function getReturnMsg($pid)
{
    $data = array();
    $tempData = M('Commentlist')->where('pid=' . $pid)->find();
    if (!empty($tempData)) {
        $data['ans_nickname'] = M('Member')->where('id=' . $tempData['mid'])->getField('nickname');
        $data['ans_userimages'] = M('Member')->where('id=' . $tempData['mid'])->getField('userimage');
        $data['ans_questions'] = $tempData['questions'];
        $data['ans_regtim'] = $tempData['regtime'];
    }
    return $data;
}

//根据id返回地址名称
function memaddress($province,$city,$district)
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

//获取当前项目是否有认购项目-有的话则显示按钮
function canBuy($proj_id)
{
    $can_buy = 0;//是否有认购项目
    if(!empty($proj_id))
    {
        $projchildM = M('Projchild');
        $w['stats'] = 1;
        $w['begintime'] = array('elt',time());
        $w['endtime'] = array('egt',time());
        $w['kind'] = 2;
        //获取开启的认领项目
        $projchildArr = $projchildM->where($w)->select();
        if(!empty($projchildArr))
        {
            $can_buy = 1;
        }
    }
    return $can_buy;
}
//获取认领资格--简短版
function canGet1($property_id)
{
    $can_get = 0;//认领资格
    $propertylistM = M('Propertylist');
    if(!empty($property_id)&&!empty($propertylistArr = $propertylistM->find($property_id)))
    {//1.资产校验
        //2.项目校验
        $proj_id = $propertylistArr['proj_id'];
        $projlistM = M('Projlist');
        if(empty($proj_id)||empty($projlistArr=$projlistM->find($proj_id)))
        {
            return $can_get;
        }

        $setBatch = strtotime(date('Y').'-01-01');
        //3.认领子项校验
        $projchildM = M('Projchild');
        $w['batchnum'] = $setBatch;
        $w['proj_id'] = $proj_id;
        $w['stats'] = 1;
        $w['begintime'] = array('elt',time());
        $w['endtime'] = array('egt',time());
        $w['kind'] = 1;
        //获取开启的认领项目
        $projchildArr = $projchildM->where($w)->select();
        if(empty($projchildArr)||empty($projchildArr[0]['minpropertynum']))
        {
            return $can_get;
        }

        //4.校验当前资产的获取时间年限
        $min_year = C('MIN_YEAR');
        if(($propertylistArr['regtime']+$min_year)>time())
        {
            return $can_get;
        }

        $can_get = 1;
    }
    return $can_get;
}
//获取认领资格--完整版（根据资产id重新校验所有）
/*
 * 参数1-资产id  $property_id
 * 参数2-认领期数,格式为‘2017-01-01’默认为当前时间年份  $batchnum
 *
 * */
function canGet2($property_id,$batchnum)
{
    $can_get['sts'] = 0;//认领资格
    $can_get['msg'] = '不满足条件';
    $propertylistM = M('Propertylist');
    if(!empty($property_id)&&!empty($propertylistArr = $propertylistM->find($property_id)))
    {//1.资产校验
        //2.项目校验
        $proj_id = $propertylistArr['proj_id'];
        $projlistM = M('Projlist');
        if(empty($proj_id)||empty($projlistArr=$projlistM->find($proj_id)))
        {
            $can_get['msg'] = '项目不存在';
            return $can_get;
        }

        //3.认领子项校验
        $projchildM = M('Projchild');
        $w['proj_id'] = $proj_id;
        $w['stats'] = 1;
        $w['begintime'] = array('elt',time());
        $w['endtime'] = array('egt',time());
        $w['kind'] = 1;
        //获取开启的认领项目
        $projchildArr = $projchildM->where($w)->select();
        if(empty($projchildArr)||empty($projchildArr['minpropertynum']))
        {
            $can_get['msg'] = '认领项目暂未开启';
            return $can_get;
        }

        //4.校验当前资产的获取时间年限
        $min_year = C('MIN_YEAR');
        if(($propertylistArr['regtime']+$min_year)>time())
        {
            $can_get['msg'] = '您的资产年限还不够';
            return $can_get;
        }

        //4.一切正常时校验当前资产是否还有认领数额--
        if($propertylistArr['kind']==1||($propertylistArr['kind']==2&&$propertylistArr['sendkind']==2))
        {//自己支持所得资产或他人赠送的未认领部分或他人赠送的已领部分已经不在一个批次内-只需要校验是否数额足够（含已领部分）
            $treenum = intval($propertylistArr['treenum']);
            $temp_canGetNum = floor($treenum/$projchildArr['minpropertynum']);//可以领取资源总数量

            unset($w);
            if(!empty($batchnum))
            {
                $w['batchnum'] = strtotime($batchnum);
            }else
            {
                $w['batchnum'] = strtotime(date('Y').'-01-01');
            }
            $w['kind'] = 1;
            $w['mid'] = $propertylistArr['mid'];
            $w['property_id'] = $property_id;
            $has_getNum = M('Claimlist')->where($w)->sum('claimnum');
            if(!empty($has_getNum))
            {
                $has_getNum = intval($has_getNum);
            }else
            {
                $has_getNum = 0;
            }

            if($temp_canGetNum-$has_getNum<=0)
            {
                $can_get['msg'] = '您的资产已不足继续认领';
                return $can_get;
            }
        }else
        {//他人赠送已认领部分
            if($propertylistArr['sendkind']==2)
            {//分为三种情况
                //a.

            }

        }

    }
    return $can_get;
}

//生成订单号
function create_uuid($prefix){    //可以指定前缀
    $str = uniqid(mt_rand(), true);
    $uuid  = date("YmdHis");
    $uuid .= substr($str,3,5);
    return $prefix . $uuid;
}

/*
     * 获取可认领的资源数
     * $batchnum  当前期号
     * $mid  会员id
     * $property_id 资产id
     * $proj_id 项目id
     * $tree 当前资产包剩余资源数量
     * */
function getMaxTreeNum($batchnum,$mid,$property_id,$proj_id,$tree)
{
    $treeNum = 0;
    //获取当期已认领的数量
    $propertylistM = M('Propertylist');
    $claimlistM = M('Claimlist');
    $w['batchnum'] = $batchnum;
    $w['kind'] = 1;
    $w['mid'] = $mid;
    $w['property_id'] = $property_id;
    $w['proj_id'] = $proj_id;
    $w['paystas'] = 1;

    $hasGetNum = $claimlistM->where($w)->sum('needtreenum');
    $hasGetNum = !empty($hasGetNum)?intval($hasGetNum):0;

    //获取当期已送出的部分资源数
    unset($w);
    $w['kind'] = 2;
    $w['ppid'] = $property_id;
    $w['sendkind'] = $batchnum;
    $w['givemid'] = $mid;
    $w['proj_id'] = $proj_id;
    $hasSendNum = $propertylistM->where($w)->sum('inittreenum');
    $hasSendNum = !empty($hasSendNum)?intval($hasSendNum):0;

    //实际可领数目计算公式 = 实际剩余数量+已送部分-已领数目
    if($tree+$hasSendNum-$hasGetNum>0)
    {
        $treeNum = $tree+$hasSendNum-$hasGetNum;
    }
    return $treeNum;
}

//根据订单获取商品列表
function getGoodsList($order_id)
{
    return D('Ordergoods')->relation(true)->where('order_id='.$order_id)->select();
}
