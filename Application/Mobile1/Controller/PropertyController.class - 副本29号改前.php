<?php
/**
 * Created by PhpStorm.
 * User: Huyh
 * Date: 2017/10/31 0031
 * Time: 上午 9:45
 */
namespace Mobile\Controller;
class PropertyController extends CommonController
{
    public $pre_session;
    public $mid;
    public function _initialize()
    {
        parent::_initialize();
        $this->pre_session = C('SESSION_PRE');
        $this->mid = $_SESSION[$this->pre_session.'user']['id']; //用户id
    }

    //我的认领----资产列表
    public function index()
    {
        cookie('is_end',0);
        $totalTree = M('Property')->where('mid='.$this->mid.' and proj_id=1')->sum('totaltree');
        if(!empty($totalTree))
        {
            $totalTree = intval($totalTree);
        }else
        {
            $totalTree = 0;
        }
        $this->totalTree = $totalTree;
        $this->display();
    }

    //资产列表ajax分页
    public function ajaxindex()
    {
        $page = 1;
        if(isset($_POST['page']))
        {
            $page = intval($_POST['page']);
        }
        $propertyM = M('Property');
        $w['totaltree'] = array('gt',0);//实际拥有的数量要大于0
        $w['mid'] = $this->mid;
        $w['proj_id'] = 1;
        $this->propertyArr = $propertyM->where($w)->order('batchnum asc')->page($page,4)->select();
        if(count($this->supportsArr)<4)
        {
            cookie('is_end',1);
        }
        $this->display();
    }

    //赠送
    public function sendProperty()
    {
        //1.校验资源
        if(!isset($_REQUEST['id'])||empty($_REQUEST['id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数丢失,禁止访问');parent.location.href='".U('Property/index')."';</script>";
            exit;
        }
        $property_id = intval($_REQUEST['id']);
        $propertyM = M('Property');
        $propertyArr = $propertyM->find($property_id);
        if(empty($propertyArr)||$propertyArr['totaltree']<=0)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您的资产剩余数量已为0,不能继续赠送');parent.location.href='".U('Property/index')."';</script>";
            exit;
        }

        if(empty($propertyArr['proj_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('项目丢失');parent.location.href='".U('Property/index')."';</script>";
            exit;
        }
        $projArr=M('Projlist')->find($propertyArr['proj_id']);

        if(empty($projArr))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('项目数据异常');parent.location.href='".U('Property/index')."';</script>";
            exit;
        }
        //2.查验本人资源在当前期数里是否存在已认领部分--这里暂时设定默认期数是当前时间的年份第一天计算
        $setBatch = strtotime(date('Y').'-01-01');

        if(IS_POST)
        {
            if(!isset($_POST['member_num'])||empty($_POST['member_num']))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('获赠爱心大使编号不能为空');parent.location.href='".U('Property/index')."';</script>";
                exit;
            }

            $member_num = intval($_POST['member_num']);
            $initNum = C('MEMBER_NUM');
            $getMid = $member_num-$initNum;
            if($getMid<=0)
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('爱心大使编号输入有误');parent.location.href='".U('Property/index')."';</script>";
                exit;
            }
            if($this->mid==$getMid)
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('不能给自己赠送资源');parent.location.href='".U('Property/index')."';</script>";
                exit;
            }

            $memberM = M('Member');
            $getMemberArr = $memberM->field('username,nickname,userphone,kind')->find($getMid);
            if(empty($getMemberArr))
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('爱心大使不存在');parent.location.href='".U('Property/index')."';</script>";
                exit;
            }
            $stats = intval($_POST['stats']);//判断资源是否属于已认领，0-已认领,1-未认领
            $sendNum = intval($_POST['sendNum']);//赠送数量

            if($propertyArr['totaltree']<$sendNum)
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('赠送数量已超出您拥有的可赠送数量');parent.location.href='".U('Property/index')."';</script>";
                exit;
            }

            M()->startTrans();
            $res1 = 1;
            $res2 = 1;
            $res3 = 1;
            $res4 = 1;
            $res5 = 1;
            $res6 = 1;
            $res7 = 1;
            $res8 = 1;
            $res9 = 1;
            $res10 = 1;

            //3.1赠送人资产明细表添加新1条新数据
            $propertylistM = M('Propertylist');
            $addData['kind'] = 4;
            $addData['ppid'] = $property_id;
            $addData['proj_id'] = $propertyArr['proj_id'];
            $addData['mid'] = $this->mid;
            $addData['initmoney'] = $projArr['salemoney'];
            $addData['inittotalmoney'] = $projArr['initmoney']*$sendNum;
            $addData['treenum'] = $sendNum;
            $addData['gettime'] = $propertyArr['batchnum'];
            $addData['regtime'] = time();
            $addData['pretreenum'] = $propertyArr['totaltree'];

            $addData['memo'] = '您赠送了'.$sendNum.'棵'.date('y',$propertyArr['batchnum']).'年的葡萄树给'.$getMemberArr['nickname'];
            if($stats==0)
            {
                $msg = '已认领';
            }else
            {
                $msg = '未认领';
            }
            $res1 = $propertylistM->add($addData);
            unset($addData);

            //3.2赠送人原资产表数据变更
            $res2 = $propertyM->where('id='.$property_id)->setInc('sendtree',$sendNum);
            $res3 = $propertyM->where('id='.$property_id)->setDec('totaltree',$sendNum);

            //3.3赠送人会员表数据变更
            $res4 = $memberM->where('id='.$this->mid)->setInc('totalout',$sendNum);

            //3.4获赠人会员表数据变更
            $res5 = $memberM->where('id='.$getMid)->setInc('totalget',$sendNum);

            if($getMemberArr['kind']==0)
            {
                $saveData['kind'] = 1;
                $saveData['joinkind'] = 2;
                $saveData['jointime'] = $propertyArr['batchnum'];
                $res6 = $memberM->where('id='.$getMid)->save($saveData);
                unset($saveData);
            }

            //3.5获赠人资产添加或修改
            $hzw['proj_id'] = $propertyArr['proj_id'];
            $hzw['mid'] = $getMid;
            $hzw['batchnum'] = $propertyArr['batchnum'];
            $hzpropertyArr = $propertyM->where($hzw)->find();
            $hzp_id = 0;//获赠人的资产id
            if(!empty($hzpropertyArr))
            {
                $res7 = $propertyM->where('id='.$hzpropertyArr['id'])->setInc('totaltree',$sendNum);
                $hzp_id = $hzpropertyArr['id'];
                $addData2['pretreenum'] = $hzpropertyArr['totaltree'];
            }else
            {
                $addData['proj_id'] =$propertyArr['proj_id'];
                $addData['mid'] = $getMid;
                $addData['batchnum'] = $propertyArr['batchnum'];
                $addData['totaltree'] = $sendNum;
                $addData['regtime'] = time();
                $res7 = $propertyM->add($addData);
                $hzp_id = $res7;
                unset($addData);
            }

            //获赠人资产列表添加
            $pre_session = C('SESSION_PRE');
            $addData2['kind'] = 3;
            $addData2['ppid'] = $property_id;
            $addData2['proj_id'] = $propertyArr['proj_id'];
            $addData2['mid'] = $getMid;
            $addData2['givemid'] = $this->mid;
            $addData2['initmoney'] = $projArr['salemoney'];
            $addData2['inittotalmoney'] = $projArr['initmoney']*$sendNum;
            $addData2['treenum'] = $sendNum;
            $addData2['gettime'] = $propertyArr['batchnum'];
            $addData2['regtime'] = time();
            $addData['memo'] = $_SESSION[$pre_session.'user']['nickname'].'给您赠送了'.$sendNum.'棵'.date('y',$propertyArr['batchnum']).'年的葡萄树';
            $res10 = $propertylistM->add($addData2);
            unset($addData2);

            //3.6若赠送的是当期已认领的资源--变更收成记录表的数据
            if($stats==0)
            {//已认领
                //赠送人记录添加
                $propertytakeM = M('Propertytake');
                $addData['mid'] = $this->mid;
                $addData['property_id'] = $property_id;
                $addData['batchnum'] = $setBatch;
                $addData['nums'] = -$sendNum;
                $addData['regtime'] = time();
                $res8 = $propertytakeM->add($addData);

                //收获人记录
                $addData['mid'] = $getMid;
                $addData['property_id'] = $hzp_id;
                $addData['batchnum'] = $setBatch;
                $addData['nums'] = $sendNum;
                $addData['regtime'] = time();
                $res9 = $propertytakeM->add($addData);
            }

            if($res1&&$res2&&$res3&&$res4&&$res5&&$res6&&$res7&&$res8&&$res9&&$res10)
            {
                M()->commit();
                $gerMemberNickname = $getMemberArr['nickname'];
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('赠送 $sendNum $msg 资源给 $gerMemberNickname 成功');parent.location.href='".U('Property/index')."';</script>";
                exit;
            }else
            {
                M()->rollback();
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('赠送失败');parent.location.href='".U('Property/index')."';</script>";
                exit;
            }
        }else
        {
            //查询是否认领
            $w['batchnum'] = $setBatch;
            $w['mid'] = $this->mid;
            $w['property_id'] = $property_id;
            $getTreeNum = M('Propertytake')->where($w)->sum('nums');
            //已认领的资源数
            if(!empty($getTreeNum))
            {
                $getTreeNum = intval($getTreeNum);
            }else
            {
                $getTreeNum = 0;
            }

            $this->getTreeNum = $getTreeNum;
            $this->totalTreeNum = $propertyArr['totaltree'];
            $this->property_id = $propertyArr['id'];
            $this->display();
        }
    }

    //收成-项目列表
    public function claimList()
    {
        $goodsM = M('Goods');
        $w['kind'] = 2;
        $w['proj_id'] = 1;
        $w['isdel'] = 0;
        $w['stats'] = 2;

        $goodsArr = $goodsM->where($w)->select();
        if(empty($goodsArr))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('请耐心等候,暂无任何收成可以领取');window.location.href='".U('User/index')."';</script>";
            exit;
        }
        $this->goodsArr = $goodsArr;
        $this->display();
    }

    //收成-明细列表ajax分页
    public function ajaxClaimList()
    {
        $page = 1;
        if(isset($_POST['page']))
        {
            $page = intval($_POST['page']);
        }

        $propertytakeM = D('Propertytake');
        $w['mid'] = $this->mid;
        $w['isself'] = 1;
        $this->propertytakeMArr = $propertytakeM->relation(true)->where($w)->order('id desc')->page($page,4)->select();
        $this->display();
    }

    //认领详情页
    public function claimDetail()
    {
        $setBatch = strtotime(date('Y').'-01-01');//当期批次
        if(!isset($_REQUEST['pc_id'])||empty($_REQUEST['pc_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误,非法请求');window.location.href='".U('Property/claimList')."';</script>";
            exit;
        }
        $goods_id = intval($_REQUEST['pc_id']);
        $goodsM=M('Goods');
        $goodsArr = $goodsM->find($goods_id);
        if(empty($goodsArr)||$goodsArr['isdel']==1||$goodsArr['kind']!=2)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您要认领的项目不存在,非法请求');window.location.href='".U('Property/index')."';</script>";
            exit;
        }

        //若用户没有填写地址，则先去填写地址
        $has_address = M('Member_address')->where('mid='.$this->mid.' and is_del=0 and is_default=1')->find();
        if(empty($has_address))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('领取收成前请先设置您的默认收货地址');window.location.href='".U('Address/addressList')."';</script>";
            exit;
        }
        $this->has_address = $has_address;
        
        //$this->batchnum = M('property')->where(array('mid'=>$this->mid,'proj_id'=>$goods_id))->select();
        $propertyArr = M('Property')->where(array('mid'=>$this->mid,'proj_id'=>$goodsArr['proj_id']))->select();

        //可领取收成总数量
        $maxGetNum = 0;
        //总自付运费
        $totalSendMoney = 0;
        //总到付运费
        $totalSendToMoney = 0;
        //总加工费
        $totalMakeMoney = 0;
        //总支付金额
        $totalPayMoney = 0;

        $treeNum = 0;
        foreach ($propertyArr as $key=>$val)
        {
            $w['mid'] = $this->mid;
            $w['property_id'] = $val['id'];
            //查询期数是否已领满9年
            $hasGetYear =  count(M('propertytake')->group('batchnum')->where($w)->select());

            //查询当前资产在本期已收成的消耗资源数量
            $w['batchnum'] = $setBatch;
            $temp_num = M('propertytake')->where($w)->sum('nums');
            if(empty($temp_num))
            {
                $temp_num = 0;
            }

            if ($hasGetYear >= $goodsArr['getmaxnum']||$temp_num>=$val['totaltree']||$val['totaltree']<=0)
            {
                unset($propertyArr[$key]);
            }else
            {
                $treeNum += $val['totaltree'];
            }
        }

        //计算总可领取收成数量
        if($goodsArr['minpropertynum']!=0)
        {
            $maxGetNum = floor($treeNum/$goodsArr['minpropertynum']);
        }
        $temp_sendMoney1 = fmod($maxGetNum,floor($goodsArr['sendmax']));
        if(empty($temp_sendMoney1))
        {
            $temp_sendMoney1 = 0;
        }
        $temp_sendMoney2 = floor($maxGetNum/$goodsArr['sendmax']);
        if($temp_sendMoney1 != 0)
        {
            $temp_sendMoney2 = $temp_sendMoney2+1;
        }
        //计算总自付运费
        if($goodsArr['sendmoney']!=0&&$goodsArr['sendmax']!=0)
        {
            $totalSendMoney = $temp_sendMoney2*$goodsArr['sendmoney'];
        }
        //计算总到付运费
        if($goodsArr['sendtomoney']!=0&&$goodsArr['sendmax']!=0)
        {
            $totalSendToMoney = $temp_sendMoney2*$goodsArr['sendtomoney'];
        }
        //计算总加工费
        if($goodsArr['omoney']!=0)
        {
            $totalMakeMoney = $maxGetNum*$goodsArr['omoney'];
        }

        //计算总支付费用
        $totalPayMoney = $totalSendMoney+$totalMakeMoney;

        //可领取收成总数量
        $this->totalSendMoney = $totalSendMoney;
        $this->totalSendToMoney = $totalSendToMoney;
        $this->totalMakeMoney = $totalMakeMoney;
        $this->totalPayMoney = $totalPayMoney;
        $this->maxGetNum = $maxGetNum;

        $this->propertyArr = $propertyArr;
        $this->assign('goodsArr',$goodsArr);
        $this->display();
    }
    //收成认领下单
    public function doClaim()
    {
        if(!IS_POST||empty($_POST['input_num']))
        {
            $jurl1 = U('Orderlist/index');
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>window.location.href='".$jurl1."'</script>";
            exit;
        }
        $goods_id = intval($_POST['goods_id']);
        $nums = intval($_POST['input_num']);//总箱数
        $order_sn = create_uuid();//生成订单号
        $percentage = 0;//余数
        $remainder = 0;//循环余数量
        $omoney = $_POST['omoney'];//加工费
        if ($_POST['kind']==0) {
            //自提
            $shipping_price = 0;//运费
            $shipping_name = '自提';//运送方式
            $total_price = 0+$omoney;//总价
        }
        if ($_POST['kind']==1) {
            //到付
            $shipping_price = $_POST['sendtomoney'];//运费
            $shipping_name = '到付';//运送方式
            $total_price = 0+$omoney;//总价
        }
        if ($_POST['kind']==2) {
            //寄付
            $shipping_price = $_POST['sendmoney'];
            $shipping_name = '';//运送方式
            $total_price = $shipping_price+$omoney;//总价
        }
        //建模
        $orderlistM = M('Orderlist');
        $ordergoodsM = M('Ordergoods');
        $goodsM = M('Goods');
        $propertyM = M('Property');
        $propertytakeM = M('Propertytake');
        $transM = M('Translog');

        $goodsArr=$goodsM->find($goods_id);
        $w['mid'] = $this->mid;
        $w['batchnum'] = $goodsArr['batchnum'];
        
        //开启事务处理本地数据
        M()->startTrans();
        $addDate['kind'] = 2;
        $addDate['batchnum'] = $goodsArr['batchnum'];
        $addDate['order_sn'] = $order_sn;
        $addDate['mid'] = $this->mid;
        $addDate['address_id'] = $_POST['address_id'];
        $addDate['order_status'] = 0;
        $addDate['pay_status'] = 0;
        $addDate['shipping_name'] = $shipping_name;
        $addDate['goods_price'] = $_POST['address_id']*$goodsArr['titles'];
        $addDate['shipping_price'] = $shipping_price;
        $addDate['other_price'] = $omoney;
        $addDate['order_amount'] = $total_price;
        $addDate['total_amount'] = $total_price;
        $addDate['regtime'] = time();
        $addDate['user_note'] = $shipping_name;
        // var_dump($addDate);
        // die;
        $res = $orderlistM->add($addDate);
        for ($a=0; $a < count($_POST['years']); $a++) { 
            $propertyArr = $propertyM->find($_POST['years'][$a]);
            $w['property_id'] = $_POST['years'][$a];
            $temp_num = $propertytakeM->where($w)->sum('nums');
            if(empty($propertyArr)||$temp_num>=$propertyArr['totaltree'])
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('您没有可领取的收成');window.location.href='".U('Index/index')."';</script>";
                exit;
            }
            
            if(!empty($temp_num))
            {
                $temp_num = intval($temp_num);
            }else
            {
                $temp_num = 0;
            }
            $bro = $percentage+$propertyArr['totaltree']-$temp_num;//可领资源
            $brought = $bro/$goodsArr['minpropertynum'];//可领数
            $percentage = ($propertyArr['totaltree']-$temp_num)%$goodsArr['minpropertynum'];//余数
            if($nums<=$brought){
                if ($remainder!=0) {
                    $num = $remainder;
                    $needtreenum = $goodsArr['minpropertynum']*$num-$percentage;
                }else{
                    $num = $nums;
                    $needtreenum = $goodsArr['minpropertynum']*$num;
                }
            }else{
                if ($remainder!=0) {
                    $num = $remainder;
                    $needtreenum = $goodsArr['minpropertynum']*$num-$percentage;
                }else{
                    $num = floor($brought);
                    $needtreenum = $bro;
                }
            }
            $remainder = $nums - floor($brought);//循环余数量
            $ogDate[$a]['order_id'] = $res;
            $ogDate[$a]['goods_id'] = $goods_id;
            $ogDate[$a]['goods_name'] = $goodsArr['titles'];
            $ogDate[$a]['goods_sn'] = $goodsArr['goods_sn'];
            $ogDate[$a]['goods_num'] = $num;
            $ogDate[$a]['goods_pic'] = $goodsArr['titlepic'];
            $ogDate[$a]['saleprice'] = $goodsArr['tmoney'];
            $ogDate[$a]['kind'] = 1;
            $ogDate[$a]['batchnum'] = $goodsArr['batchnum'];
            $ogDate[$a]['property_id'] = $_POST['years'][$a];
            $ogDate[$a]['proj_id'] = $goodsArr['proj_id'];
            $ogDate[$a]['minpropertynum'] = $goodsArr['minpropertynum'];
            $ogDate[$a]['needtreenum'] = $needtreenum;
        }
        // var_dump($ogDate);
        // die;
        $res1 = $ordergoodsM->addAll($ogDate);
        //到付或自提且加工费为0
        if ($total_price==0&&$res&&$res1) {
            //校验支付信息
            $orderArr = $orderlistM->find($res);
            $order_status = 1;//订单状态
            if ($orderArr['shipping_name']=='自提') {
                $order_status = 4;
            }
            $orderlistM->where('id='.$res)->save(array('order_status' => $order_status,'paykind' => 0,'pay_status' => 1,'pay_time' => time()));
            //添加资产变更表
            
            $ogArr = $ordergoodsM->where(array('order_id'=>$res))->select();
            for ($i=0; $i < count($ogArr); $i++) { 
                $propertyArr = $propertyM->find($ogArr[$i]['property_id']);
                if(empty($propertyArr))
                {
                    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                    echo "<script type='text/javascript'>alert('您没有可领取的收成');window.location.href='".U('Index/index')."';</script>";
                    exit;
                }
                unset($protakeData);
                $protakeData['isself'] = 1;
                $protakeData['mid'] = $this->mid;
                $protakeData['property_id'] = $ogArr[$i]['property_id'];
                $protakeData['batchnum'] = $ogArr[$i]['batchnum'];
                $protakeData['goods_id'] = $ogArr[$i]['goods_id'];
                $protakeData['order_id'] = $res;
                $protakeData['nums'] = $ogArr[$i]['needtreenum'];
                $protakeData['regtime'] = time();
                //var_dump($ogArr[$i]['goods_id']);
                $propertytake = $propertytakeM->add($protakeData);
            }
            //4.交易记录表添加数据
            unset($saveData);
            $saveData['mid'] = $this->mid;
            $saveData['typeid'] = 2;
            $saveData['tagid'] = $res;
            $saveData['paykind'] = 0;
            $saveData['tmoney'] = $total_price;
            $saveData['memo'] = $shipping_name;
            $saveData['regtime'] = time();
            $trans = $transM->add($saveData);
            if ($propertytake&&$trans) {
                M()->commit();
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('收成领取成功');window.location.href='".U('Orderlist/index')."';</script>";
                exit;
            }else{
                M()->rollback();
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('收成领取失败');window.location.href='".U('Property/claimlist')."';</script>";
                exit;
            }
            
        }else{
            //寄付
            if($res&&$res1){
                M()->commit();
                $jurl = "http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/goodsPay";
                //$jurl = "http://localhost/jsshamo/index.php/Mobile/Wxpay/goodsPay";
                $pre_session = C('SESSION_PRE');
                session($pre_session,$res);
                unset($_POST);
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>window.location.href='".$jurl."';</script>";
                exit;
            }else{
                M()->rollback();
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('收成领取失败');window.location.href='".U('Property/claimlist')."';</script>";
                exit;
            }
        }
        
        
    }
    //确认认领页面
    public function orderConfirm()
    {
        //若没有地址则先设置地址
        $address = M('Member_address')->where(array("mid"=>$this->mid,"is_default"=>1))->find();
        if(empty($address))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('请先设置您的默认收货地址');window.location.href='".U('Address/addressList')."';</script>";
            exit;
        }

        if(!isset($_POST['saleNum'])||empty($_POST['saleNum'])||!isset($_POST['pc_id'])||empty($_POST['pc_id'])||!isset($_POST['property_id'])||empty($_POST['property_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('非法请求');window.location.href='".U('Property/index')."';</script>";
            exit;
        }

        $pc_id = intval($_POST['pc_id']);
        $projchildM = M('Projchild');

        $this->saleNum = intval($_POST['saleNum']);
        $this->property_id = intval($_POST['property_id']);
        $this->projchildArr=$projchildM->find($pc_id);
        $this->ordernum = create_uuid();
        //收货地址
        $this->address = $address;
        $pre_session  = C('SESSION_PRE');
        $this->memberArr = $_SESSION[$pre_session.'user'];
        $this->display();
    }

    //认领下单
    public function doOrderConfirm()
    {
        if(!IS_POST)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('非法请求');window.location.href='".U('Property/index')."';</script>";
            exit;
        }

        //获取认领数量
        if(!isset($_POST['saleNum'])||empty($_POST['saleNum']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('认领数量不能小于1件');window.location.href='".U('Property/index')."';</script>";
            exit;
        }
        $saleNum = intval($_POST['saleNum']);

        //当前期号
        $batchnum = strtotime(date('Y').'-01-01');

        $projchildM = M('Projchild');
        //获取认领产品id
        if(!isset($_POST['pc_id'])||empty($_POST['pc_id'])||empty($projchildArr=$projchildM->find($_POST['pc_id']))||$projchildArr['stats']==0||time()<$projchildArr['begintime']||time()>$projchildArr['endtime'])
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('当期认领的产品未开启');window.location.href='".U('Property/index')."';</script>";
            exit;
        }

        //获取资产
        $propertylistM = M('Propertylist');
        if(!isset($_POST['property_id'])||empty($_POST['property_id'])||empty($propertyArr=$propertylistM->find($_POST['property_id']))||$propertyArr['sendkind']==$batchnum||$propertyArr['treenum']<=0)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您的资产发生变动,请重新认领');window.location.href='".U('Property/index')."';</script>";
            exit;
        }

        //计算当期可兑换的最大资源数
        $hasTreeNum = getMaxTreeNum($batchnum,$this->mid,$propertyArr['id'],$propertyArr['proj_id'],$propertyArr['treenum']);

        if(floor($saleNum*$projchildArr['minpropertynum'])>$hasTreeNum)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您拥有的资产数量不足以认领当前数量的产品');window.location.href='".U('Property/index')."';</script>";
            exit;
        }

        $addDate['batchnum'] = $batchnum;
        $addDate['ordernum'] = $_POST['ordernum'];
        $addDate['kind'] = 1;
        $addDate['mid'] = $this->mid;
        $addDate['address_id'] = $_POST['address_id'];
        $addDate['property_id'] = $propertyArr['id'];
        $addDate['proj_id'] = $propertyArr['proj_id'];
        $addDate['pc_id'] = $projchildArr['id'];
        $addDate['claimnum'] = $saleNum;
        $addDate['minpropertynum'] = $projchildArr['minpropertynum'];
        $addDate['needtreenum'] = $projchildArr['minpropertynum']*$saleNum;
        $addDate['claimprice'] = $projchildArr['pc_money'];
        $addDate['othermoney'] = $projchildArr['pc_omoney'];
        $addDate['totalmoney'] = ($projchildArr['pc_money']+$projchildArr['pc_omoney'])*$saleNum;
        $addDate['regtime'] = time();

        if($res = M("Claimlist")->add($addDate))
        {
            $jurl = "http://sjapi.yiwo15.com/jsshamo/index.php/Mobile/Wxpay/claimPay";
            $pre_session = C('SESSION_PRE');
            session($pre_session,$res);
            unset($_POST);
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>window.location.href='".$jurl."';</script>";
            exit;
        }else
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('认领失败');history.back();</script>";
            exit;
        }
    }


    //认领记录页
    public function claimRecord()
    {
        cookie('is_end',0);
        $this->display();
    }
    //认领记录ajax分页
    public function ajaxClaimRecord()
    {
        $page = 1;
        if(isset($_POST['page']))
        {
            $page = intval($_POST['page']);
        }
        $propertylistM = M('propertylist');
        //$w['totaltree'] = array('gt',0);//实际拥有的数量要大于0
        $w['mid'] = $this->mid;
        $w['proj_id'] = 1;
        $this->propertylistArr = $propertylistM->where($w)->order('regtime desc')->page($page,10)->select();
        if(count($this->propertylistArr)<=10)
        {
            cookie('is_end',1);
        }
        $this->display();
    }



}