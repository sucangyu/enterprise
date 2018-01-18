<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Use:公司介绍
 * Date: 2016/7/15
 * Time: 11:27
 */

namespace Admin\Controller;
use Think\AjaxPage;
use Think\Page;

class CompanyController extends CommonController
{
    /*公司详情*/
    public function index()
    {
        $enprM = M('Enpr_info');
        $enprArr = $enprM->find();
        $regionM = M('Region');
        $regArr = $regionM->where(array('parent_id' =>0 ))->select();
        $shufflingM=M('shuffling');
        $shufflingArr = $shufflingM->where(array('ep_id' =>$enprArr['id']))->select();
        M()->startTrans();
        if(IS_POST){
            //var_dump($_POST);die;
            $data['name']=$_POST['comp_name'];
            $data['desc']=$_POST['comp_desc'];
            $data['pic']=$_POST['comp_pic'];
            $data['log_img']=$_POST['log_img'];
            $data['phone']=$_POST['comp_phone'];
            $data['fax']=$_POST['comp_fax'];
            $data['email']=$_POST['comp_email'];
            $data['QQ']=$_POST['comp_QQ'];
            $data['country']=0;
            $data['province']=$_POST['pre'];
            $data['city']=$_POST['city'];
            $data['district']=$_POST['area'];
            $data['twon']=0;
            $data['address']=$_POST['detailed'];
            $data['zipcode']=$_POST['comp_zipcode'];
            $data['link']=$_POST['comp_link'];
            $data['details']=$_POST['comp_content'];
            
            
            if(!$enprArr){
                //如果公司不存在执行添加
                $data['regtime']=time();
                $res = $enprM->add($data);
                for ($i=0; $i < count($_POST['comp_images']); $i++) { 
                    $imgs[$i]['img_src']=$_POST['comp_images'][$i];
                    $imgs[$i]['ep_id']=$res;
                }
                $res1 = $shufflingM->addAll($imgs);
                if ($res&&$res1) {
                    M()->commit();
                    $this->show("<script>alert('公司详情添加成功');location.href='".U('Company/index')."'</script>");
                    exit;
                }else{
                    M()->rollback();
                    $this->show("<script>alert('公司详情添加失败');location.href='".U('Company/index')."'</script>");
                    exit;
                }
            }else{
                //存在执行修改
                $row = $enprM->where(array('id'=>$enprArr['id']))->save($data);//修改公司信息
                for ($i=0; $i < count($_POST['comp_images']); $i++) { 
                    $imgs[$i]['img_src']=$_POST['comp_images'][$i];
                    $imgs[$i]['ep_id']=$enprArr['id'];
                }
                $del = $shufflingM->where(array('ep_id'=>$enprArr['id']))->delete();
                $add = $shufflingM->addAll($imgs);//修改轮播图
                if($row||$add){
                    M()->commit();
                    exit($this->success('修改成功',U('Company/index')));
                }
                M()->rollback();
                exit($this->error('未作内容修改或修改失败'));
            }
        }
        $this->assign('reg', $regArr);
        $this->assign('shufflingArr', $shufflingArr);
        $this->assign('enprArr',$enprArr);
        //var_dump($enprArr);
        $this->display();
    }
        
    //ajax地址联动
    public function ajaxCityList(){
        $regionM = M('Region');
        if(!$_POST||empty($_POST['lid']))
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo "<script type='text/javascript'>alert('获取地区参数错误');window.location.href='".U('User/index')."';</script>";
            exit;
        }
        $list = $regionM->where(array('parent_id' =>$_POST['lid']))->select();
        exit(json_encode($list));
    }

    //首页轮播图
    public function imglist()
    {
        $this->display();
    }

    /**
     * 首页轮播图列表
     */
    public function ajaximglist(){
        // 搜索条件
        $condition = array();
        I('alt') ? $condition['alt'] = array('like','%'.I('alt').'%') : false;
        $sort_order = I('order_by').' '.I('sort');

        $shufflingM=M('shuffling');
        $count = $shufflingM->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val)
        {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $shuList = $shufflingM->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();
        $show = $Page->show();
        $this->assign('shuList',$shuList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    /**
     * 添加图片页面
     */
    public function addimg()
    {
        $enprM = M('Enpr_info');
        $shufflingM=M('shuffling');
        $enprArr = $enprM->find();
        M()->startTrans();
        if($_POST){
            //  项目信息编辑
            $data['ep_id']=$enprArr['id'];
            $data['img_src']=$_POST['img_src'];
            $data['alt']=$_POST['alt'];
            $data['a_href']=$_POST['a_href'];
            $row = $shufflingM->add($data);
            if ($row) {
                M()->commit();
                $this->show("<script>alert('图片添加成功');location.href='".U('Company/imglist')."'</script>");
                exit;
            }else{
                M()->rollback();
                $this->show("<script>alert('图片添加失败');location.href='".U('Company/imglist')."'</script>");
                exit;
            }
        }
        $this->display();
    }
    /**
     * 轮播图详细信息查看
     */
    public function imgdetail()
    {
        $imgid = I('get.img_id');
        if (!$imgid) {
            $imgid = I('post.img_id');
        }
        $shufflingM=M('shuffling');
        $shufflingArr = $shufflingM->find($imgid);
        M()->startTrans();
        if(!$shufflingArr)
            exit($this->error('图片不存在'));
        if($_POST){
            //  项目信息编辑
            $data['img_src']=$_POST['img_src'];
            $data['alt']=$_POST['alt'];
            $data['a_href']=$_POST['a_href'];
     

            $row = $shufflingM->where(array('id'=>$imgid))->save($data);
            if ($row) {
                M()->commit();
                $this->show("<script>alert('图片修改成功');location.href='".U('Company/imglist')."'</script>");
                exit;
            }else{
                M()->rollback();
                $this->show("<script>alert('图片修改失败');location.href='".U('Company/imglist')."'</script>");
                exit;
            }
        }
        //$projArr['projimages'] = json_decode($projArr['projimages']);
        $this->assign('shufflingArr',$shufflingArr);
        $this->display();
    }

    //删除操作
    public function imgdel(){
        $id = $_GET['img_id'];

        $res = M('shuffling')->where("id = ".$id)->delete();
        if($res){
            $return_arr = array('status'=>1,'msg' => '操作成功','data' =>'');
            $this->ajaxReturn(json_encode($return_arr));
        }else{
            $return_arr = array('status'=>0,'msg' => '操作失败','data' =>'');
            $this->ajaxReturn(json_encode($return_arr));
        }

    }

    //友情链接
    public function linkList()
    {
        $this->display();
    }
    /**
     * 友情链接列表
     */
    public function ajaxlinklist(){
        // 搜索条件
        $condition = array();
        I('name') ? $condition['name'] = array('like','%'.I('name').'%') : false;
        $sort_order = I('order_by').' '.I('sort');

        $linkM=M('link');
        $count = $linkM->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val)
        {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $linkList = $linkM->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();
        $show = $Page->show();
        $this->assign('linkList',$linkList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    /**
     * 添加友情链接页面
     */
    public function addlink()
    {
        $linkM=M('link');
        M()->startTrans();
        if($_POST){
            //  项目信息编辑
            $data['name']=$_POST['name'];
            $data['url']=$_POST['url'];

            $row = $linkM->add($data);
            if ($row) {
                M()->commit();
                $this->show("<script>alert('友情链接添加成功');location.href='".U('Company/linkList')."'</script>");
                exit;
            }else{
                M()->rollback();
                $this->show("<script>alert('友情链接添加失败');location.href='".U('Company/linkList')."'</script>");
                exit;
            }
        }
        $this->display();
    }

    /**
     * 添加友情详细信息查看
     */
    public function linkdetail()
    {
        $id = I('get.id');
        if (!$id) {
            $id = I('post.id');
        }
        $linkM=M('link');
        $linkArr = $linkM->find($id);
        M()->startTrans();
        if(!$linkArr)
            exit($this->error('链接不存在'));
        if($_POST){
            //  项目信息编辑
            $data['name']=$_POST['name'];
            $data['url']=$_POST['url'];

            $row = $linkM->where(array('id'=>$id))->save($data);
            if ($row) {
                M()->commit();
                $this->show("<script>alert('友情链接修改成功');location.href='".U('Company/linkList')."'</script>");
                exit;
            }else{
                M()->rollback();
                $this->show("<script>alert('友情链接修改失败');location.href='".U('Company/linkList')."'</script>");
                exit;
            }
        }
        //$projArr['projimages'] = json_decode($projArr['projimages']);
        $this->assign('linkArr',$linkArr);
        $this->display();
    }

    //友情链接删除操作
    public function linkdel(){
        $id = $_GET['id'];

        $res = M('link')->where("id = ".$id)->delete();
        if($res){
            $return_arr = array('status'=>1,'msg' => '操作成功','data' =>'');
            $this->ajaxReturn(json_encode($return_arr));
        }else{
            $return_arr = array('status'=>0,'msg' => '操作失败','data' =>'');
            $this->ajaxReturn(json_encode($return_arr));
        }

    }
}