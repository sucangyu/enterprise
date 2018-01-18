<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Use: 新闻管理
 * Date: 2017/10/24
 * Time: 16:36
 */

namespace Admin\Controller;
use Think\AjaxPage;

class NewslistController extends CommonController
{
    public function index()
    {   
        
        $this->display();
    }

    /**
     * 新闻列表
     */
  
    public function ajaxIndex(){
        //var_dump($_POST);
        $condition= array();
        $condition['isdel']=0;
        if(I('uid')!=''){
            $condition['uid']=I('uid');
        }

        $timegap = I('time');
        if($timegap)
        {
            $gap = explode('-',$timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1]);
        }

        //搜索条件

        if(I('title')!='')
        {   $title = I('title');
            $condition['title'] = array('like',"%$title%");
        }

        if($begin && $end)
        {
            $condition['time'] = array('between',"$begin,$end");
        }



        $newM = M('New');
        $sort_order = I('order_by','DESC').' '.I('sort');
        $count = $newM->where($condition)->count();
        $Page = new  AjaxPage($count,10);

        $newlist = $newM-> where($condition) ->order($sort_order)-> limit($Page->firstRow.','.$Page->listRows)->select();

        //var_dump($newM->select());
        $show = $Page->show();
        $this->assign('newlist',$newlist);
        $this->assign('page',$show);
        $this->display();
    }

    /**
     * 添加新闻页面
     */
    public function addNew()
    {
        $newM=M('new');
        //echo $_SESSION[C('USER_AUTH_KEY')]."啊啊啊啊啊啊啊啊啊啊";
        M()->startTrans();
        if($_POST){
            //  项目信息编辑
            $data['uid']=$_SESSION[C('USER_AUTH_KEY')];
            $data['title']=$_POST['title'];
            $data['paperimg']=$this->_getImg($_POST['content']) ;
            $data['paper']=$this->_getSummary($_POST['content']);
            $data['browser']=0;
            $data['content']=$_POST['content'];
            $data['tag']=$_POST['tag'];
            $data['time']=time();
            $data['isdel']=0;
            // var_dump($data['paperimg']);
            // die;
            $row = $newM->add($data);
            if ($row) {
                M()->commit();
                $this->show("<script>alert('新闻添加成功');location.href='".U('Newslist/index')."'</script>");
                exit;
            }else{
                M()->rollback();
                $this->show("<script>alert('新闻添加失败');location.href='".U('Newslist/index')."'</script>");
                exit;
            }
        }
        $this->display();
    }
    /**
     * 新闻详细信息查看
     */
    public function detail()
    {
        $cid = I('get.id');
        if (!$cid) {
            $cid = I('post.id');
        }
        $newM=M('new');
        $newArr = $newM->find($cid);
        
         M()->startTrans();
        if($_POST){
            //  项目信息编辑
            $data['title']=$_POST['title'];
            $data['paperimg']=$this->_getImg($_POST['content']) ;
            $data['paper']=$this->_getSummary($_POST['content']);
            $data['content']=$_POST['content'];
            $data['tag']=$_POST['tag'];
            // var_dump($data['paperimg']);
            // die;
            $row = $newM->where("id = ".$cid)->save($data);
            if ($row) {
                M()->commit();
                $this->show("<script>alert('新闻修改成功');location.href='".U('Newslist/index')."'</script>");
                exit;
            }else{
                M()->rollback();
                $this->show("<script>alert('新闻修改失败');location.href='".U('Newslist/index')."'</script>");
                exit;
            }
        }
        if(!$newArr)
            exit($this->error('详情不存在'));
        $newArr['uid'] = $this->userId($newArr['uid']);
        $this->assign('newArr',$newArr);
        $this->display();
    }

    //删除操作
    public function del(){
        $id = $_GET['id'];

        $newlist = M('new')->where("id = ".$id)->save(array('isdel'=>1));
        if($newlist){
            $return_arr = array('status'=>1,'msg' => '操作成功','data' =>'');
            $this->ajaxReturn(json_encode($return_arr));
        }else{
            $return_arr = array('status'=>0,'msg' => '操作失败','data' =>'');
            $this->ajaxReturn(json_encode($return_arr));
        }

    }


    //根据id查名称
    public function userId($id){
        $userM=M('user');
        $userArr = $userM->find($id);
        return $userArr['username'];
    }

    /*私有文章简介截取方法*/
    private function _getSummary($content,$s = 0,$e = 150,$char = 'utf-8')
    {
        if (empty($content)) {
            return null;
        }
        return (mb_substr(str_replace('&nbsp;', '', strip_tags($content)), $s, $e, $char));
    }

    /*私有文章图片提取方法*/
    private function _getImg($str)
    {
        if (empty($str)) {
            return null;
        }
        $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern,$str,$match);
        return $match[1][0];
    }
}