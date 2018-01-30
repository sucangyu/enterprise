<?php
//招聘
namespace Home\Controller;
use Think\Controller;
class CurrencyController extends Controller
{
    //首页
    public function index()
    {
        $objectsM=M('Objects');
        $objects = $objectsM->where('is_disabled=0')->order('body_rank desc')->select();
        $this->assign(array(
            'controller'=>'objectsController',
            'objects'=>$objects,
        ));
        $this->display();
        
    }
    //关于我们
    public function objectsDetail()
    {
        $oid = I('get.id');
        $type = I('get.type');
      // die;
        $id = 1;
        $userM = D('Users');
        $objectsM=M('Objects');
        $user = $userM->find($id);
        //if ($user['is_disabled'] > 0) return $this->denialUser();
        $object = $objectsM->find($oid);
        $this->assign(array(
            'navigator' => 'objects',
            'controller' => 'objectsDetailController',
            'user' => $user,
            'item' => $object,
            'period' => $type
        ));
    	  
        //$this->assign('deparList',$deparList);
        $this->display('detail');
    }

}