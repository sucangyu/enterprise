<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Use:会员管理
 * Date: 2016/7/15
 * Time: 11:27
 */

namespace Admin\Controller;
use Think\AjaxPage;
use Think\Page;

class CommentController extends CommonController
{
    public function index()
    {
        $this->display();
    }

    /**
     * 会员列表
     */
    public function ajaxindex(){
        // 搜索条件
        $condition = array();
        if(I('proj_id')!='') {
            $sql='select id from Projlist where titles like "%'.I('proj_id').'%"';
            $proj_id=M()->query($sql);
            if($proj_id) {
                foreach($proj_id as $v){
                    $proj[]=$v['id'];
                }
                $condition['proj_id'] = array('in', $proj);
            }else{
                $this->assign('userList','');
                $this->display();exit;
            }
        }
        if(I('mid')!='') {
            $sql='select id from Member where nickname like "%'.I('mid').'%" or username like "%'.I('mid').'%"';
            $user_id=M()->query($sql);
            if($user_id) {
                foreach($user_id as $v){
                    $user[]=$v['id'];
                }
                $condition['mid'] = array('in', $user);
            }else{
                $this->assign('userList','');
                $this->display();exit;
            }
        }
        if(I('isreturn')!='') {
            $condition['isreturn'] = I('isreturn');
        }
        $condition['admin_id']=0;
        $sort_order = I('order_by').' '.I('sort');

        $model = M('Commentlist');
        $count = $model->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val)
        {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $userList = $model->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($userList as $k=>$v){
            $username=M('Member')->find($v['mid']);
            $userList[$k]['mid']=$username['nickname'];
            $proj=M('Projlist')->find($v['proj_id']);
            $userList[$k]['proj_id']=$proj['titles'];
            $pid=M('User')->find($v['admin_id']);
            $userList[$k]['admin_id']=$pid['nickname'];
        }
        $show = $Page->show();
        $this->assign('userList',$userList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    /**
     * 会员详细信息查看
     */
    /**
     * 回复咨询
     */
    public function reply(){
        if(I('get.id')) {
            $id = I('get.id');
        }
        if($_POST){
            if(empty($_POST[goods_content])){
                exit($this->error('请填写回复内容'));
            }
            $com=D('Commentlist');
            $arr['proj_id']=$_POST['proj_id'];
            $arr['mid']=$_POST['mid'];
            $arr['pid']=$_POST['pid'];
            $arr['questions']=$_POST['goods_content'];
            $arr['admin_id']=$_SESSION['admin_id'];
            $arr['regtime']=time();
            $res=$com->add($arr);
            if($res){
                $id=$_POST['pid'];
                $data['isreturn'] = 1;
                $com->where('id='.$id)->save($data); // 根据条件更新记录
            }


        }
        $user=M('Member')->select();
        $sql='select * from Commentlist where id='.$id.' or pid='.$id;
        $comment=M()->query($sql);
        foreach($comment as $k=>$v){
            $home_user=M('Member')->find($v[mid]);
            $comment[$k]['nickname']=$home_user['nickname'];
            $comment[$k]['userimage']=$home_user['userimage'];
        }
        $this->assign('user',$user);
        $this->assign('comment',$comment);
        $this->display();
    }
    /**
     * 删除
     */
    public function del(){
        $com=M('Commentlist');
        $id=I('get.id');
        $data=array('isdel'=>1);
        $row=$com->where('id='.$id)->setField($data);
        if($row)
            exit($this->success('删除成功',U('index')));
        exit($this->error('删除失败'));
    }
}