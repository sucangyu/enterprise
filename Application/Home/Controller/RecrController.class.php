<?php
//招聘
namespace Home\Controller;
use Think\Controller;
class RecrController extends CommonController
{
    //首页
    public function index()
    {
        $shufflingM=M('shuffling');
        $newM=M('new');
        $shufflingArr = $shufflingM->select();
        $newlist = $newM->where('isdel=0')->order('time desc')->select();

        $recruitmentM = D('recruitment');        // 实例化侧栏一级导航对象
        $count = $recruitmentM->where($condition)->count();// 查询满足要求的总记录
        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显
        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $recruitmentM->order('regtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('shufflingArr', $shufflingArr);
        $this->assign('newlist', $newlist);
        $this->display();
        
    }
    //关于我们
    public function detail()
    {
    	$id = I('get.id');
        $recruitmentM = D('recruitment');
        $recruiArr = $recruitmentM->relation(true)->find($id);
        if(!$recruiArr)
            exit($this->error('详情不存在'));

        switch($recruiArr['schooling']) 
        {
         case $recruiArr['schooling']==1:
          $recruiArr['schooling'] = "中专";
          break;
         case $recruiArr['schooling']==2:
          $recruiArr['schooling'] = "大专";
          break;
         case $recruiArr['schooling']==3:
          $recruiArr['schooling'] = "本科";
          break;
         case $recruiArr['schooling']==4:
          $recruiArr['schooling'] = "硕士";
          break;
         case $recruiArr['schooling']==5:
          $recruiArr['schooling'] = "博士";
          break;
         default:
          $recruiArr['schooling'] = "不限";
        }

        switch($recruiArr['experience']) 
        {
         case $recruiArr['experience']==1:
          $recruiArr['experience'] = "可接受应届毕业生";
          break;
         case $recruiArr['experience']==2:
          $recruiArr['experience'] = "1~3年";
          break;
         case $recruiArr['experience']==3:
          $recruiArr['experience'] = "3~5年";
          break;
         case $recruiArr['experience']==4:
          $recruiArr['experience'] = "5年以上";
          break;
         default:
          $recruiArr['experience'] = "不限";
        }

        $this->assign('recruiArr',$recruiArr);

        $departmentM=M('department');
        $deparList = $departmentM->limit(2)->select();
        $this->assign('deparList',$deparList);
        $this->display();
    }
    
}