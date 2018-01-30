<?php
//招聘
namespace Home\Controller;
use Think\Controller;
class ApiController extends Controller
{
    //首页
    public function objects()
    {
        $result = array(
          //'timestamp' => time(),
          'objects' => array()
        );
        $userM = D('Users');
        $objectsM=M('Objects');
        if ($id = 1) {
          $result['user'] = $userM->find($id);
        }
        $result['objects'] = $objectsM->where('is_disabled=0')->order('body_rank desc')->select();
        $this->ajaxReturn($result);
        
    }
    public function objectsDetail()
    {
      $id = I('get.id');
      $type = I('get.type');
      $objectsM=M('Objects');
      $linesM=M('lines');
      $object = $objectsM->find($id);

      $lines = $linesM->where(array('id_object'=>$object['id'],'body_period'=>$type))->order('id desc')->select();
      if ($object['body_status'] == 1) {
        $object['status'] = TRUE;
      } else {
        $object['status'] = FALSE;
      }
      $result = array(
        //'timestamp' => time(),
        'period' => $type,
        'object' => $object,
        'lines' => $lines
      );
      $this->ajaxReturn($result);
    }
    
    public function objectsDetailUpdate()
    {
      
      $id = I('get.id');
      $type = I('get.type');
      $objectsM=M('Objects');
      $linesM=M('lines');
      $object = $objectsM->find($id);
      $lines = $linesM->where(array('id_object'=>$object['id'],'body_period'=>$type))->order('id desc')->find();
      if ($object['body_status'] == 1) {
        $object['status'] = TRUE;
      } else {
        $object['status'] = FALSE;
      }
      
      $result = array(
        //'timestamp' => time(),
        'period' => $type,
        'object' => $object,
        'lines' => $lines
      );
      $this->ajaxReturn($result);
      
    }

    

}