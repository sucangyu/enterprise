<?php
namespace Home\Controller;
use Think\Controller;
class GoodsController extends CommonController
{
    //首页
    public function index()
    {
        
        $shufflingM=M('shuffling');
        $newM=M('new');
        $goodsM = M('Goods');
        $shufflingArr = $shufflingM->order('id desc')->select();
        $newlist = $newM->where('isdel=0')->order('time desc')->select();
        $goodlist = $goodsM->order('regtime desc,is_recommend desc')->select();
        $this->assign('shufflingArr', $shufflingArr);
        $this->assign('newlist', $newlist);
        $this->assign('goodlist', $goodlist);
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