<?php
namespace Home\Controller;
use Think\Controller;
class NewsController extends CommonController
{
    //首页
    public function index()
    {
        
        $shufflingM=M('shuffling');
        $newM=M('new');
        $goodsM = M('Goods');
        $shufflingArr = $shufflingM->select();
        $newlist = $newM->where('isdel=0')->order('time desc')->select();
        $goodlist = $goodsM->order('regtime desc,is_recommend desc')->limit(3)->select();
        $this->assign('shufflingArr', $shufflingArr);
        $this->assign('newlist', $newlist);
        $this->assign('goodlist', $goodlist);
        $this->display();
        
    }
    //新闻详情页
    public function detail()
    {
    	$cid = I('get.id');
        $newM=M('new');
        $newArr = $newM->find($cid);
        if(!$newArr)
            exit($this->error('详情不存在'));
        $newArr['uid'] = $this->userId($newArr['uid']);
        $newM->where('id='.$cid)->setInc('browser',1); //browser字段值自增1,setDec是自减
        $this->assign('newArr',$newArr);
        $this->display();
    }

    //根据id查名称
    public function userId($id){
        $userM=M('user');
        $userArr = $userM->find($id);
        return $userArr['username'];
    }


    
}