<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Use:  商品管理
 * Date: 2017/8/17
 * Time: 10:22
 */


namespace Admin\Controller;
use Think\AjaxPage;
use Think\Page;

class GoodsController extends CommonController
{
    
    //商品列表
    public function goodList()
    {   

        $this->display();
    }

    
    //ajax商品列表
    public function ajaxGoodsList()
    {

        $where = array();
        // 搜索条件
        if(I('goods_name')!='')
        {   $goods_name = I('goods_name');
            $where['goods_name'] = array('like',"%$goods_name%");
        }
        if(I('is_recommend')!='')
        {   
            $where['is_recommend'] = I('is_recommend');
        }


        $model = M('Goods');
        $count = $model->where($where)->count();

        
        $Page  = new AjaxPage($count,10);
        $show = $Page->show();

        $order_str = I('orderby1').' '.I('orderby2');

        $goodsList = $model->where($where)->order($order_str)->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('goodsList',$goodsList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    public function goods()
    {
        $cationArr = M('classification')->select();
        $this->assign('cationArr',$cationArr);
        $this->display(_goods);
    }

    //添加商品
    public function addEditGoods()
    {
        if (!isset($_POST)&&empty($_POST)){
            $this->show("<script>alert('参数错误');location.href='".U('Goods/goodsList')."'</script>");
            exit;
        }
        // var_dump($_POST);
        // die;
        $data['goods_name']=$_POST['goods_name'];
        $data['goods_remark']=$_POST['goods_remark'];
        $data['goods_sn']=$_POST['goods_sn'];
        $data['cat_id']=$_POST['cat_id'];
        $data['keywords'] = $_POST['keywords'];
        $data['is_recommend']=$_POST['stats'];
        $data['original_img']=json_encode($_POST['pics']);
        $data['market_price'] = $_POST['tmoney'];
        $data['shop_price']=$_POST['shop_price'];
        $data['cost_price']=$_POST['cost_price'];
        $data['store_count']=$_POST['nums'];
        $data['weight']=$_POST['weight'];
        $data['goods_content']=$_POST['desc'];
        $data['nums']=$_POST['nums'];
        $data['regtime'] =time();
        $goodsM=M('goods');
        M()->startTrans();
        if ($goodsM->add($data)) {
            M()->commit();
            $this->show("<script>alert('商品添加成功');location.href='".U('Goods/goodsList')."'</script>");
            exit;
        }else{
            M()->rollback();
            $this->show("<script>alert('商品添加失败');location.href='".U('Goods/goodsList')."'</script>");
            exit;
        }


    }

    /**
     * 查看商品详情
     */
    public function detail()
    {
        $goodid = I('get.id');
        if (!$goodid) {
            $goodid = I('post.id');
        }
        $goodsM=M('goods');
        $goodsInfo = $goodsM->find($goodid);
        $cationArr = M('classification')->select();
        
        if(!goodsInfo)
            exit($this->error('商品不存在'));
        if($_POST){
//            商品编辑
            $data['goods_name']=$_POST['goods_name'];
            $data['goods_remark']=$_POST['goods_remark'];
            $data['goods_sn']=$_POST['goods_sn'];
            $data['cat_id']=$_POST['cat_id'];
            $data['keywords'] = $_POST['keywords'];
            $data['is_recommend']=$_POST['stats'];
            $data['original_img']=json_encode($_POST['pics']);
            $data['market_price'] = $_POST['tmoney'];
            $data['shop_price']=$_POST['shop_price'];
            $data['cost_price']=$_POST['cost_price'];
            $data['store_count']=$_POST['nums'];
            $data['weight']=$_POST['weight'];
            $data['goods_content']=$_POST['desc'];
            $data['nums']=$_POST['nums'];
            //$data['regtime'] =time();
            
            $row = $goodsM->where('goods_id='.$goodid)->save($data);
            if ($row) {
                M()->commit();
                $this->show("<script>alert('商品修改成功');location.href='".U('Goods/goodsList')."'</script>");
                exit;
            }else{
                M()->rollback();
                $this->show("<script>alert('商品修改成功');location.href='".U('Goods/goodsList')."'</script>");
                exit;
            }
        }
//        var_dump($goodsInfo);
        $goodsInfo['original_img'] = json_decode($goodsInfo['original_img']);
        $this->assign('cationArr',$cationArr);
        $this->assign('goodsInfo',$goodsInfo);
        $this->display();
    }

    public function ajaxdetail()
    {

    }

    /**
     * 删除商品
     */
    public function delGoods()
    {

        $goods_id = $_GET['id'];
        $data['isdel'] = 1;
        $data['id'] = $goods_id;
//        echo $goods_id;
//        $isMe = M('Goods')->where('goods_id='.$goods_id)->count();
//        if(!$isMe)
//        {
//            $return_arr = array(
//                'status' => 0,
//                'msg' => '您没有权限删除非本商户的产品',
//                'data' => '',
//            );
//            $this->ajaxReturn(json_encode($return_arr));
//        }
//
//        // 删除此商品
        M("Goods")->save($data);  //商品表
        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
        $this->ajaxReturn(json_encode($return_arr));
    }


    //商品分类
    public function cationList() 
    {
        $this->display();
    }
    /**
     * 商品分类列表
     */
    public function ajaxcationlist(){
        // 搜索条件
        $condition = array();
        I('title') ? $condition['title'] = array('like','%'.I('title').'%') : false;
        $sort_order = I('order_by').' '.I('sort');

        $cationM=M('classification');
        $count = $cationM->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val)
        {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $cationList = $cationM->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();
        $show = $Page->show();
        $this->assign('cationList',$cationList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    /**
     * 添加商品分类页面
     */
    public function addcation()
    {
        $cationM=M('classification');
        M()->startTrans();
        if($_POST){
            //  项目信息编辑
            $data['title']=$_POST['title'];
            $data['regtime']=$_POST['regtime'];

            $row = $cationM->add($data);
            if ($row) {
                M()->commit();
                $this->show("<script>alert('分类添加成功');location.href='".U('Goods/cationList')."'</script>");
                exit;
            }else{
                M()->rollback();
                $this->show("<script>alert('分类添加失败');location.href='".U('Goods/cationList')."'</script>");
                exit;
            }
        }
        $this->display();
    }

    /**
     * 商品分类详细信息查看
     */
    public function cationdetail()
    {
        $id = I('get.id');
        if (!$id) {
            $id = I('post.id');
        }
        $cationM=M('classification');
        $cationArr = $cationM->find($id);
        M()->startTrans();
        if(!$cationArr)
            exit($this->error('分类不存在'));
        if($_POST){
            //  项目信息编辑
            $data['title']=$_POST['title'];

            $row = $cationM->where(array('id'=>$id))->save($data);
            if ($row) {
                M()->commit();
                $this->show("<script>alert('分类修改成功');location.href='".U('Goods/cationList')."'</script>");
                exit;
            }else{
                M()->rollback();
                $this->show("<script>alert('分类修改失败');location.href='".U('Goods/cationList')."'</script>");
                exit;
            }
        }
        //$projArr['projimages'] = json_decode($projArr['projimages']);
        $this->assign('cationArr',$cationArr);
        $this->display();
    }

    //商品分类删除操作
    public function cationdel(){
        $id = $_GET['id'];

        $res = M('classification')->where("id = ".$id)->delete();
        if($res){
            $return_arr = array('status'=>1,'msg' => '操作成功','data' =>'');
            $this->ajaxReturn(json_encode($return_arr));
        }else{
            $return_arr = array('status'=>0,'msg' => '操作失败','data' =>'');
            $this->ajaxReturn(json_encode($return_arr));
        }

    }

}