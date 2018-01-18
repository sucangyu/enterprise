<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController
{
    //首页
    public function index()
    {
        
        $shufflingM=M('shuffling');
        $newM=M('new');
        $goodsM = M('Goods');
        $departmentM=M('department');
        $deparList = $departmentM->select();
        $shufflingArr = $shufflingM->select();
        $newlist = $newM->where('isdel=0')->order('time desc')->select();
        $goodlist = $goodsM->order('regtime desc,is_recommend desc')->limit(3)->select();
        $this->assign('shufflingArr', $shufflingArr);
        $this->assign('newlist', $newlist);
        $this->assign('goodlist', $goodlist);
        $this->assign('deparList',$deparList);
        $this->assign('deparNum',count($deparList));
        $this->display();
        
    }
    //关于我们
    public function aboutMy()
    {
    	$shufflingM=M('shuffling');
        $shufflingArr = $shufflingM->select();
        $departmentM=M('department');
        $deparList = $departmentM->select();
        $this->assign('deparList',$deparList);
        $this->assign('shufflingArr', $shufflingArr);
    	$this->display();
    }
    
}