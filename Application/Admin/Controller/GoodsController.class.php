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
//        // 搜索条件
        //(I('goods_name') !== '') && $where = "goods_name = ".I('goods_name');
        if(I('goods_name')!='')
        {   $goods_name = I('goods_name');
            $where['goods_name'] = array('like',"%$goods_name%");
        }

        // 关键词搜索
        // $key_word = I('key_word') ? trim(I('key_word')) : '';
        // if($key_word)
        // {
        //     $where = "$where and (goods_name like '%$key_word%' or goods_sn like '%$key_word%')" ;
        // }

        $model = M('Goods');
        $count = $model->where($where)->count();

        
        $Page  = new AjaxPage($count,10);
        $show = $Page->show();

        $order_str = I('orderby1').' '.I('orderby2');

        $goodsList = $model->where($where)->order($order_str)->limit($Page->firstRow.','.$Page->listRows)->select();

        // var_dump($goodsList);
        $this->assign('goodsList',$goodsList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    public function goods()
    {
        // $goodArr = M('Goods')->select();
        // $this->assign('goodArr',$goodArr);
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
        //$projArr = M('Projlist')->select();
        if(!goodsInfo)
            exit($this->error('商品不存在'));
        if($_POST){
//            商品编辑
            $data['goods_name']=$_POST['goods_name'];
            $data['goods_remark']=$_POST['goods_remark'];
            $data['goods_sn']=$_POST['goods_sn'];
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
        //$this->assign('projArr',$projArr);
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


}