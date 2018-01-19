<?php
namespace Home\Controller;
use Think\Controller;
use Think\AjaxPage;
use Think\Page;
class GoodsController extends CommonController
{
    //首页
    public function index()
    {
        
        $shufflingM=M('shuffling');
        $newM=M('new');
        $cationM = M('classification');
        $shufflingArr = $shufflingM->order('id desc')->select();
        $newlist = $newM->where('isdel=0')->order('time desc')->select();
        $cationlist = $cationM->select();
        $this->assign('shufflingArr', $shufflingArr);
        $this->assign('newlist', $newlist);
        $this->assign('cationlist', $cationlist);
        $this->display();
        
    }

    /**
     * 商品列表
     */
    public function ajaxindex(){
        // 搜索条件
        $condition = array();

        if(isset($_REQUEST['cat_id'])&&$_REQUEST['cat_id']!='')
        {
            $condition['cat_id'] = $_REQUEST['cat_id'];
        }
        $sort_order = 'regtime desc,is_recommend desc';
        $goodsM = M('Goods');
        $count = $goodsM->where($condition)->count();
        $Page  = new AjaxPage($count,6);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val)
        {
            $Page->parameter[$key]   =   urlencode($val);
        }

        $goodlist = $goodsM->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();
        //var_dump($goodlist);
        $show = $Page->show();
        $this->assign('goodlist',$goodlist);
//        var_dump($goodlist);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }



    //关于我们
    public function detail()
    {
        $cid = I('get.id');
    	$goodsM = M('Goods');
        $newM=M('new');
        $newlist = $newM->where('isdel=0')->order('time desc')->select();
        $goodArr = $goodsM->find($cid);
        $goodArr['original_img'] = json_decode($goodArr['original_img']);
        if(!$goodArr)
            exit($this->error('详情不存在'));

        $this->assign('newlist', $newlist);
        $this->assign('goodArr', $goodArr);
        if(isMobile()){
        //跳转移动端页面
            $this->display(_detail);
        }else{
        //跳转PC端页面
            $this->display();
        }
    	
    }
    
}