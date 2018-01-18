<?php
namespace Admin\Controller;
use Think\Controller;
use Org\Util\Rbac;
class IndexController extends Controller
{
    public function _initialize()
    {
        $configM = M('Config');
        $configArr = $configM->select();
        $this->store_name = '大周科技';
        $this->site_url = 'http://www.huyan.space';
        if(!empty($configArr))
        {
            foreach($configArr as $key=>$val)
            {
                if($val['name']=='store_name')
                {
                    $this->store_name = $val['value'];
                }
                if($val['name']=='site_url')
                {
                    $this->site_url = $val['value'];
                }
            }
        }
    }
    //验证码
    public function verify()
    {
        $Verify = new \Think\Verify();
        $Verify->length = 4;
        $Verify->fontSize = 50;
        $Verify->useImgBg = true;
        $Verify->codeSet = '0123456789';
        $Verify->entry();
    }
    //后台管理员登录页面
    public function index()
    {
        $this->display();
    }
    //登录表单处理
    public function login()
    {
        //1.验证码验证
        $Verify = new \Think\Verify();
        $code = I('post.verify');
        if(!$Verify->check($code))
        {
            $this->error('验证码错误',U('Index/index'));
        }
        if(IS_POST)
        {
            //用户名和密码验证
            $userM = D('User');
            $data['username'] = I('post.username');
            $data['password'] = md5(I('post.password'));
            $userArr = $userM->relation(true)->where($data)->find();
            if(!empty($userArr))
            {
                if($userArr['status']==0)
                {
                    $this->error('当前用户已被禁用',U('Index/index'));
                }

                $saveData['user_id'] = $userArr['id'];
                $saveData['log_time'] = time();
                $saveData['log_ip'] = getIP();

                $saveData2['logintime'] = $saveData['log_time'];
                $saveData2['loginIP'] = $saveData['log_ip'];

                $userM->startTrans();
                $res2 = $userM->where('id='.$userArr['id'])->save($saveData2);
                $res1 = M('Login_log')->add($saveData);

                if($res1 && $res2)
                {
                    $userM->commit();
                    session('admin_name',$userArr['username']);
                    session('admin_id',(int)$userArr['id']);
                    session('admin_role',$userArr['Role']['name']);

                    //登录判断，与配置表设置信息相对应的
                    session(C('USER_AUTH_KEY'),$userArr['id']);
                    if($_SESSION['admin_name'] == C('RBAC_SUPERADMIN'))
                    {
                        session(C('ADMIN_AUTH_KEY'),true);
                    }
                    //RBAC
                    $rbac = new Rbac();
                    $rbac->saveAccessList();
                    $this->success('登录成功',U('Main/index'));
                }else
                {
                    $userM->rollback();
                    $this->error('获取用户ip地址失败');
                }
            }else
            {
                $this->error('用户名或密码错误');
            }
        }
    }

    //退出系统
    public function login_out()
    {
        session(null);
        if(empty($_SESSION))
        {
            $this->success('退出成功',U('Index/index'));
        }
        else
        {
            $this->error('退出失败');
        }
    }

    /**
     * ajax 修改指定表数据字段  一般修改状态 比如 是否推荐 是否开启 等 图标切换的
     * table,id_name,id_value,field,value
     */
/*    public function changeTableVal()
    {
        $table = I('table'); // 表名
        $id_name = I('id_name'); // 表主键id名
        $id_value = I('id_value'); // 表主键id值
        $field  = I('field'); // 修改哪个字段
        $value  = I('value'); // 修改字段值
        M($table)->where("$id_name = $id_value")->save(array($field=>$value)); // 根据条件保存修改的数据
    }*/

    /**
     * 会员账号ajax校验
     * kd=0:会员账号校验
     * kd=1:赠送人校验
     */
    public function checkHy()
    {
        $data['stsc'] = 0;
        $data['msg'] = '未知错误';

        if(!isset($_POST['mid'])||empty($_POST['mid']))
        {
            $data['msg'] = '账号为空';
            $this->ajaxReturn($data);
        }
        $mid = $_POST['mid']-C('MEMBER_NUM');
        if($mid<=0)
        {
            $data['msg'] = '账号错误';
            $this->ajaxReturn($data);
        }
        $mM = M('Member');
        $menberArr = $mM->find($mid);
        if(empty($menberArr))
        {
            $data['msg'] = '当前爱心大使不存在,请更换';
            $this->ajaxReturn($data);
        }
        $data['stsc'] = 1;
        $data['msg'] = '请核对当前爱心大使昵称：'.$menberArr['nickname'].',姓名：'.$menberArr['username'];
        $this->ajaxReturn($data);
    }
}