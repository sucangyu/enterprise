<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Use:部门管理
 * Date: 2016/7/15
 * Time: 11:27
 */

namespace Admin\Controller;
use Think\AjaxPage;
use Think\Page;

class DepartmentController extends CommonController
{
    public function index()
    {
        $this->display();
    }

    /**
     * 部门列表
     */
    public function ajaxindex(){
        // 搜索条件
        $condition = array();
        I('title') ? $condition['title'] = array('like','%'.I('title').'%') : false;
        //$sort_order = I('order_by').' '.I('sort').;
        I('head') ? $condition['head'] = array('like','%'.I('head').'%') : false;

        $departmentM = M('department');
        $count = $departmentM->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val)
        {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $deparList = $departmentM->where($condition)->limit($Page->firstRow.','.$Page->listRows)->select();
        $show = $Page->show();
        $this->assign('deparList',$deparList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    /**
    *发送邮件
    */
    public function sendEmail(){
        $email = $_POST['email'];
        $title = $_POST['title'];
        $content =$_POST['content'];

        if (substr($email, 0-strlen(",")) == ",") {
            rtrim($email, ",");
        }
        if (strstr($email,",")) {
            $email = explode(",",$email);
        }else{
            $email[0] = $email;
        }
        
        for ($i=0; $i < count($email); $i++) {
            if (!$email||!filter_var($email[$i], FILTER_VALIDATE_EMAIL)) {
                
                $this->error("邮件格式不正确,请重新发送");
            }
        }
        if (!$title) {
            $this->error('请填写标题');
        }
        if (!$content) {
            $this->error('请填写发送内容');
        }
        $return = send_email($email,$title,$content);
        if ($return['error'] == 0) {
            $this->success('发送成功');
        }else{
            $this->error($return['message']);
        }
        
    }
    /**
     * 添加部门页面
     */
    public function addDepartment()
    {
        $this->display();
    }
    //删除部门方法
    public function depardel()
    {
        $id = $_GET['id'];

        $res = M('department')->where("id = ".$id)->delete();
        if($res){
            $return_arr = array('status'=>1,'msg' => '操作成功','data' =>'');
            $this->ajaxReturn(json_encode($return_arr));
        }else{
            $return_arr = array('status'=>0,'msg' => '操作失败','data' =>'');
            $this->ajaxReturn(json_encode($return_arr));
        }

    }
    /**
     * 添加部门方法
     */
    public function addDepartmentForm()
    {
        if (!isset($_POST)&&empty($_POST)){
            $this->show("<script>alert('参数错误');location.href='".U('Department/addDepartment')."'</script>");
            exit;
        }
        $data['title']=$_POST['title'];
        $data['head']=$_POST['head'];
        $data['dep_img']=$_POST['dep_img'];
        $data['content']=$_POST['content'];

        $departmentM=M('department');
        M()->startTrans();
        if ($departmentM->add($data)) {
            M()->commit();
            $this->show("<script>alert('部门添加成功');location.href='".U('Department/index')."'</script>");
            exit;
        }else{
            M()->rollback();
            $this->show("<script>alert('部门添加失败');location.href='".U('Department/index')."'</script>");
            exit;
        }
        
    }
    /**
     * 部门详细信息查看
     */
    public function detail()
    {
        $deparid = I('get.id');
        if (!$deparid) {
            $deparid = I('post.id');
        }
        $departmentM=M('department');
        $deparArr = $departmentM->find($deparid);

        if(!$deparArr)
            exit($this->error('部门不存在'));
        if($_POST){
            //  部门信息编辑
            $data['title']=$_POST['title'];
            $data['head']=$_POST['head'];
            $data['dep_img']=$_POST['dep_img'];
            $data['content']=$_POST['content'];
            
            $row = $departmentM->where(array('id'=>$deparid))->save($data);
            if ($row) {
                M()->commit();
                $this->show("<script>alert('部门修改成功');location.href='".U('Department/index')."'</script>");
                exit;
            }else{
                M()->rollback();
                $this->show("<script>alert('部门修改失败');location.href='".U('Department/index')."'</script>");
                exit;
            }
        }
        //$projArr['projimages'] = json_decode($projArr['projimages']);
        $this->assign('deparArr',$deparArr);
        $this->display();
    }


    //招聘
    public function recruiList()
    {
        $this->display();
    }

    /**
     * 招聘列表
     */
    public function ajaxRecruiList(){
        // 搜索条件
        $condition = array();
        I('position') ? $condition['position'] = array('like','%'.I('position').'%') : false;
        //$sort_order = I('order_by').' '.I('sort');
        $recruitmentM = D('recruitment');
        $count = $recruitmentM->where($condition)->count();

        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val)
        {
            $Page->parameter[$key]   =   urlencode($val);
        }
        $recruiList = $recruitmentM->where($condition)->relation(true)->order('regtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $show = $Page->show();
        // var_dump($recruiList).'hfdjhg;sahg;oa';
        // die;
        $this->assign('recruiList',$recruiList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    /**
     * 添加招聘信息页面
     */
    public function addRecruitment()
    {
        $departmentM=M('department');
        $deparList = $departmentM->select();
        $this->assign('deparList',$deparList);
        $this->display();
    }
    /**
     * 添加招聘信息方法
     */
    public function addRecruitmentForm()
    {
        if (!isset($_POST)&&empty($_POST)){
            $this->show("<script>alert('参数错误');location.href='".U('Department/addRecruitment')."'</script>");
            exit;
        }
        $data['title']=$_POST['title'];
        $data['position']=$_POST['position'];
        $data['salary']=$_POST['salary'];
        $data['treatment']=$_POST['treatment'];
        $data['nums']=$_POST['nums'];
        $data['schooling']=$_POST['schooling'];
        $data['experience']=$_POST['experience'];
        $data['dep_id'] = $_POST['dep_id'];
        $data['publisher']=$_POST['publisher'];
        $data['content']=$_POST['content'];
        $data['regtime']=time();
        $recruitmentM=M('recruitment');
        M()->startTrans();
        if ($recruitmentM->add($data)) {
            M()->commit();
            $this->show("<script>alert('招聘信息添加成功');location.href='".U('Department/recruiList')."'</script>");
            exit;
        }else{
            M()->rollback();
            $this->show("<script>alert('招聘信息添加失败');location.href='".U('Department/recruiList')."'</script>");
            exit;
        }
        
    }
    /**
     * 招聘信息详情
     */
    public function recruiDetail()
    {
        $recruiid = I('get.id');
        if (!$recruiid) {
            $recruiid = I('post.id');
        }
        $recruitmentM = D('recruitment');
        $recruiArr = $recruitmentM->relation(true)->find($recruiid);
        $departmentM=M('department');
        $deparList = $departmentM->select();
        $this->assign('deparList',$deparList);
        if(!$recruiArr)
            exit($this->error('招聘信息不存在'));
        if($_POST){
            //  项目信息编辑
            $data['title']=$_POST['title'];
            $data['position']=$_POST['position'];
            $data['salary']=$_POST['salary'];
            $data['treatment']=$_POST['treatment'];
            $data['nums']=$_POST['nums'];
            $data['schooling']=$_POST['schooling'];
            $data['experience']=$_POST['experience'];
            $data['dep_id'] = $_POST['dep_id'];
            $data['publisher']=$_POST['publisher'];
            $data['content']=$_POST['content'];

            $row = $recruitmentM->where(array('id'=>$recruiid))->save($data);
            if ($row) {
                M()->commit();
                $this->show("<script>alert('项目修改成功');location.href='".U('Department/recruiList')."'</script>");
                exit;
            }else{
                M()->rollback();
                $this->show("<script>alert('项目修改成功');location.href='".U('Department/recruiList')."'</script>");
                exit;
            }
        }
        $this->assign('deparList',$deparList);
        $this->assign('recruiArr',$recruiArr);
        $this->display();
    }

}