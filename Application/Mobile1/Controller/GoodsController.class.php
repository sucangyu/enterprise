<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/1 0001
 * Time: 下午 3:59
 */
namespace Mobile\Controller;
class GoodsController extends CommonController
{
    public $pre_session;
    public $mid;
    public function _initialize()
    {
        parent::_initialize();
        $this->pre_session = C('SESSION_PRE');
        $this->mid = $_SESSION[$this->pre_session.'user']['id']; //用户id
    }
    //粮仓列表首页
    public function index()
    {
        cookie('is_end',0);
        $this->display();
    }
    //ajax粮仓列表
    public function ajaxindex()
    {
        $page=I('page',1);
        $child = M('Goods')->where('stats = 2 and isdel = 0 and kind = 1')->order('minpropertynum asc ,regtime desc')->page($page,6)->select();
        if(count($child)<6)
        {
            cookie('is_end',1);
        }

        $this->assign('time',time());
        $this->assign('sr',$child);
        $this->display();
    }
    //粮仓详情
    public function details()
    {
        $jurl = U('Goods/index');
        if(!isset($_GET['good_id'])||empty($_GET['good_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误,非法请求');window.location.href='".$jurl."';</script>";
            exit;
        }
        $good_id = intval($_GET['good_id']);
        $goodsM = M('Goods');
        $cartlistM = M('Cartlist');
        $goodsArr = $goodsM->find($good_id);
        if(empty($goodsArr)||$goodsArr['isdel']==1)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您要认领的项目不存在,非法请求');window.location.href='".$jurl."';</script>";
            exit;
        }
        $goodsArr['pics'] = json_decode($goodsArr['pics']);
        $num = $cartlistM->where(array('mid' =>$this->mid ))->sum('goods_num');
        if (!$num) {
            $num = 0;
        }
        $this->assign('num',$num);
        $this->assign('goodsArr',$goodsArr);
        $this->display();
    }
    //添加购物车方法
    public function addCart(){
        $jurl = U('Goods/index');
        if (empty($_POST['good_id'])||!isset($_POST['good_id'])) {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误,非法请求');window.location.href='".$jurl."';</script>";
            exit;
        }
        $good_id = intval($_POST['good_id']);
        $goodsM = M('Goods');
        $cartlistM = M('Cartlist');
        $goodsArr = $goodsM->find($good_id);
        $isCart = $cartlistM->where(array('mid' =>$this->mid,'goods_id'=> $good_id))->find();
        if ($isCart) {
            $num = $isCart['goods_num']+1;
            if ($num<=$goodsArr['nums']) {
                $isNum = $cartlistM->where(array("id" => $isCart['id']))->save(array("goods_num" => $num));
            }
            $this->show("<script>location.href='".U('Goods/cart',array('gid'=>$good_id))."'</script>");
            exit;
        }else{
            $data['mid'] = $this->mid;
            $data['goods_sn'] = $goodsArr['goods_sn'];
            $data['goods_id'] = $good_id;
            $data['goods_name'] = $goodsArr['titles'];;
            $data['goods_num'] = 1;
            $data['market_price'] = $goodsArr['tmoney'];;
            $data['vip_price'] = $goodsArr['vipmoney'];;
            $data['selected'] = 1;
            $data['add_time'] = time();
            $data['prom_type'] = 0;
            $res = $cartlistM->add($data);
            if($res){
                //$this->success("加入购物车成功",U("cart/index"));
                $this->show("<script>location.href='".U('Goods/cart',array('gid'=>$good_id))."'</script>");
                exit;
            }else{
                //$this->error("加入购物车失败",U("index"));
                $this->show("<script>location.href='".U('Goods/cart?good_id='.$good_id)."'</script>");
                exit;
            }
        }
        
    }
    //购物车页
    public function cart(){
        $cartlistM = M('cartlist as a');
        $goodsM = M('Goods');
        //修改是否选中状态
        if (!empty($_POST['cid'])) {
            $isCart = $cartlistM->where(array("id" => $_POST['cid']))->save(array("selected" => $_POST['type']));
            $this->ajaxReturn($isCart);

        }
        $cartlist = $cartlistM->field('*,a.id as cid')->join('Goods as b on a.goods_id = b.id')->where(array('mid' =>$this->mid ,'stats'=>2,'isdel'=>0))->select();
        //修改购买数量
        if (!empty($_POST['cartID'])) {
            $goodsNum = $cartlistM->field('*,a.id as cid')->join('Goods as b on a.goods_id = b.id')->where(array('a.id' =>$_POST['cartID'] ))->find();
            if ($_POST['nums']>$goodsNum['nums']) {
                $data['type'] = 2;
                $data['msg'] = $goodsNum['nums'];
                $this->ajaxReturn($data);
            }else{
                $isNum = $cartlistM->where(array("id" => $_POST['cartID']))->save(array("goods_num" => $_POST['nums']));
                $data['type'] = $isNum;
                $this->ajaxReturn($data);
            }
        }
        $this->goodid = intval($_GET['gid']);
        $this->assign('cartlist',$cartlist);
        $this->display();
    }
    //删除购物车
    public function delete(){

        $cartlistM = M('cartlist');
        $id = $_POST['cid'];
        $this->ajaxReturn($cartlistM->delete($id));
    }
    //粮仓确认页面
    public function order()
    {   
        if(!isset($_REQUEST['good_id'])||empty($_REQUEST['good_id'])||!isset($_REQUEST['good_num'])||empty($_REQUEST['good_num']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('参数错误,非法请求');window.location.href='".U('Goods/index')."';</script>";
            exit;
        }
        $mid = $this->mid;
        $good_id = intval($_REQUEST['good_id']);
        $good_num = intval($_REQUEST['good_num']);
        //商品信息
        $this->goodArr = M('Goods')->find($good_id);
        $this->memberArr=M('Member')->find($mid);
        $this->ordernum = create_uuid();
        $this->saleNum = $good_num;
        //$mid = 1;
        //收货地址
        $address = M('Member_address')->where(array("mid"=>$mid,"is_default"=>1))->find();
        if($address)
        {
           $address['pcd'] = $this->getAddressName($address['province'],$address['city'],$address['district']);
        }
        // var_dump($mid);
        //var_dump($this->GoodsArr);
        $this->assign('address',$address);
        $this->display();
    }    
    
    //生成立即购买订单
    public function doOrder(){
        if(!IS_POST)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('非法访问');history.back();</script>";
            exit;
        }
        if(!isset($_POST['address_id'])||empty($_POST['address_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('请去添加收货地址');history.back();</script>";
            exit;
        }

       
        $goodnum = intval($_POST['goodnum']);
        $goods_id = intval($_POST['goods_id']);
        $orderlistM = M('Orderlist');
        $ordergoodsM = M('Ordergoods');
        $goodsM = M('Goods');
        $memberM = M('Member');
        //开启事务处理本地数据
        M()->startTrans();
        $memberArr=$memberM->find($this->mid);
        //防止重复提交
        $orderArr=$orderlistM->where(array('order_sn'=>$_POST['ordersn']))->find();
        if (!empty($orderArr)) {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您已下过此订单,请勿重新下单');window.location.href='".U('Goods/index')."';</script>";
            exit;
        }
        //获取认领产品id
        if(!isset($goods_id)||empty($goods_id)||empty($goodsArr=$goodsM->find($goods_id))||$goodsArr['nums']<$goodnum)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('此产品库存不足请从新选择');window.location.href='".U('Goods/index')."';</script>";
            exit;
        }
        $Gprice = $goodsArr['tmoney'];
        if ($memberArr['kind']==1) {
            $Gprice = $goodsArr['vipmoney'];
        }
        if ($_POST['kind']==2) {
            //自提
            $shipping_name = '自提';//运送方式
        }
        if ($_POST['kind']==1) {
            //到付
            $shipping_name = '到付';//运送方式
        }
        if ($_POST['kind']==3) {
            //寄付
            $shipping_name = '';//运送方式
        }
        $addDate['kind'] = 1;
        $addDate['order_sn'] = $_POST['ordersn'];
        $addDate['mid'] = $this->mid;
        $addDate['address_id'] = $_POST['address_id'];
        $addDate['order_status'] = 0;
        $addDate['pay_status'] = 0;
        $addDate['shipping_name'] = $shipping_name;
        $addDate['goods_price'] = $goodnum*$Gprice;
        $addDate['shipping_price'] = $_POST['sendmoy'];
        $addDate['order_amount'] = $goodnum*$Gprice+$_POST['sendmoy'];
        $addDate['total_amount'] = $goodnum*$Gprice+$_POST['sendmoy'];
        $addDate['regtime'] = time();
        $addDate['user_note'] = $_POST['msg'];
        $res = $orderlistM->add($addDate);
        $ogDate['order_id'] = $res;
        $ogDate['goods_id'] = $goods_id;
        $ogDate['goods_name'] = $goodsArr['titles'];
        $ogDate['goods_sn'] = $goodsArr['goods_sn'];
        $ogDate['goods_num'] = $goodnum;
        $ogDate['goods_pic'] = $goodsArr['titlepic'];
        $ogDate['saleprice'] = $Gprice;
        $ogDate['kind'] = 0;
        $res1 = $ordergoodsM->add($ogDate);
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
            echo "<script type='text/javascript'>alert('下单失败');history.back();</script>";
            exit;
        }
    }

    //粮仓购物车确认页面
    public function orders()
    {   
        $cartlistM = M('cartlist as a');
        $mid = $this->mid;
        //购物车商品信息
        $this->cartlist = $cartlistM->field('*,a.id as cid')->join('Goods as b on a.goods_id = b.id')->where(array('mid' =>$mid,'selected'=>1))->select();
        
        $this->memberArr=M('Member')->find($mid);
        $this->ordernum = create_uuid();
        //收货地址
        $address = M('Member_address')->where(array("mid"=>$mid,"is_default"=>1))->find();
        if($address)
        {
           $address['pcd'] = $this->getAddressName($address['province'],$address['city'],$address['district']);
        }
        $this->assign('address',$address);
        $this->display();
    }
    //生成购物车购买订单
    public function doOrders(){
        if(!IS_POST)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('非法访问');history.back();</script>";
            exit;
        }
        if(!isset($_POST['address_id'])||empty($_POST['address_id']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('请去添加收货地址');history.back();</script>";
            exit;
        }

        $goodnum = $_POST['goodnum'];
        $goods_id = $_POST['goods_id'];
        if ($memberArr['kind']==1) {
            $Gprice = $goodsArr['vipmoney'];
        }
        $shipping_price = $_POST['sendmoy'];//总运费
        $orderlistM = M('Orderlist');
        $ordergoodsM = M('Ordergoods');
        $goodsM = M('Goods');
        $memberM = M('Member');
        $cartlistM = M('Cartlist');
        //开启事务处理本地数据
        M()->startTrans();
        //防止重复提交
        $orderArr=$orderlistM->where(array('order_sn'=>$_POST['ordersn']))->find();
        if (!empty($orderArr)) {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('您已下过此订单,请勿重新下单');window.location.href='".U('Goods/index')."';</script>";
            exit;
        }
        $memberArr=$memberM->find($this->mid);
        //获取产品id
        for ($i=0; $i < count($goods_id); $i++) { 
            $goodsArr=$goodsM->find($goods_id[$i]);
            //var_dump($goodsArr);
            if(empty($goodsArr)||$goodsArr['nums']<$goodnum[$i])
            {
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo "<script type='text/javascript'>alert('此产品库存不足请从新选择');window.location.href='".U('Goods/index')."';</script>";
                exit;
            }
            $Gprice = $goodsArr['tmoney'];
            if ($memberArr['kind']==1) {
                $Gprice = $goodsArr['vipmoney'];
            }
            $Gprices += $goodnum[$i]*$Gprice;//商品总价
        }
        $zj = $Gprices;//总价
        if ($_POST['kind']==2) {
            //自提
            $shipping_name = '自提';//运送方式
        }
        if ($_POST['kind']==1) {
            //到付
            $shipping_name = '到付';//运送方式
        }
        if ($_POST['kind']==3) {
            //寄付
            $shipping_name = '';//运送方式
            $zj = $Gprices+$shipping_price;//总价
        }
        $addDate['kind'] = 1;
        $addDate['order_sn'] = $_POST['ordersn'];
        $addDate['mid'] = $this->mid;
        $addDate['address_id'] = $_POST['address_id'];
        $addDate['order_status'] = 0;
        $addDate['pay_status'] = 0;
        $addDate['shipping_name'] = $shipping_name;
        $addDate['goods_price'] = $Gprices;
        $addDate['shipping_price'] = $shipping_price;
        $addDate['order_amount'] = $zj;
        $addDate['total_amount'] = $zj;
        $addDate['regtime'] = time();
        $addDate['user_note'] = $_POST['msg'];
        // var_dump($addDate);
        // die;
        $res = $orderlistM->add($addDate);
        for ($a=0; $a < count($goods_id); $a++) { 
            $goodsArr2=$goodsM->find($goods_id[$a]);
            $Gprice2 = $goodsArr2['tmoney'];
            if ($memberArr['kind']==1) {
                $Gprice2 = $goodsArr2['vipmoney'];
            }
            $ogDate[$a]['order_id'] = $res;
            $ogDate[$a]['goods_id'] = $goods_id[$a];
            $ogDate[$a]['goods_name'] = $goodsArr2['titles'];
            $ogDate[$a]['goods_sn'] = $goodsArr2['goods_sn'];
            $ogDate[$a]['goods_num'] = $goodnum[$a];
            $ogDate[$a]['goods_pic'] = $goodsArr2['titlepic'];
            $ogDate[$a]['saleprice'] = $Gprice2;
            $ogDate[$a]['kind'] = 0;
        }
        
        $res1 = $ordergoodsM->addAll($ogDate);
        $res2 = $cartlistM->where(array("mid"=>$this->mid,"selected"=>1))->delete();
        if($res&&$res1&&$res2){
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
            echo "<script type='text/javascript'>alert('下单失败');history.back();</script>";
            exit;
        }
    }
    //新建地址页面
    public function addmanaddress() {
        $model_region = M('region');
        $good1 = array();
        $list = $model_region->where(array('parent_id' => 0))->select();
        for ($one = 0;$one < count($list);$one++) {
            $temp_arr1 = array('id' => $list[$one]['id'], 'name' => $list[$one]['name'],);
            array_push($good1, $temp_arr1);
        }
        $this->good1 = $good1;
        $this->assign('tree', json_encode($good1));
        $this->assign('type', $_GET);
        $this->display();
    }
    //新建地址方法
    public function doAddAddress()
    {
        $good_id = I('param.good_id');
        $good_num = I('param.good_num');
        $type = I('param.type');
        $jurl = U('Goods/orders',array('good_id'=>$good_id,'good_num'=>$good_num));
        if ($type==2) {
            $jurl = U('Goods/orders');
        }
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
//        $addData['is_default']=1;
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
    //获取地址
    public function getAddressName($p=0,$c=0,$d=0)
    {
        $p = M('Region')->where(array('id'=>$p))->field('name')->find();
        $c = M('Region')->where(array('id'=>$c))->field('name')->find();
        $d = M('Region')->where(array('id'=>$d))->field('name')->find();
        return $p['name'].','.$c['name'].','.$d['name'].',';
    }


}