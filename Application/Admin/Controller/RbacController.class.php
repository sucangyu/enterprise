<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Use：权限管理
 * Date: 2016/6/12
 * Time: 14:05
 */
namespace Admin\Controller;
use Org\Util\Tree;
use Think\Controller;

class RbacController extends CommonController
{
    //角色列表
    public function roleList()
    {
        $this->roleList = M('Role')->select();
        $this->display();
    }
    //添加角色
    public function addRole()
    {
        $this->display();
    }
    //添加角色表单处理
    public function do_addRole()
    {
        $roleM = M('Role');
        $roleM->create();
        if($roleM->add())
        {
            $this->success('角色添加成功',U('Rbac/roleList'));
        }else
        {
            $this->error('角色添加失败');
        }
    }
    //编辑角色
    public function editRole()
    {
        if(!isset($_GET['id']))
        {
            $this->error('参数丢失');
        }
        $id = I('get.id/d');
        $roleM = M('Role');
        $this->roleList = $roleM->find($id);
        $this->display();
    }
    //编辑角色表单处理
    public function do_editRole()
    {
        if(!isset($_POST['id']) || empty($_POST['id']))
        {
            $this->error('角色id参数丢失');
        }
        $roleM = M('Role');
        $roleM->create();
        if($roleM->save())
        {
            $this->success('角色修改成功',U('Rbac/roleList'));
        }else
        {
            $this->error('角色修改失败');
        }
    }
    //删除角色
    public function delRole()
    {
        if(!isset($_GET['id']))
        {
            $this->error('参数丢失');
        }
        $id = I('get.id/d');

        //需要删除与角色管理的权限表和更新用户角色表
        $roleM = M('Role');
        $accessM = M('Access');
        $role_userM = M('RoleUser');

        $roleArr = $roleM->find($id);
        $accArr = $accessM->where('role_id='.$id)->select();
        $r_uArr = $role_userM->where('role_id='.$id)->select();
        //开启事务处理
        $roleM->startTrans();
        $res1 = 1;
        $res2 = 1;
        $res3 = 1;

        if(!empty($roleArr))
        {
            $res1 = $roleM->delete($id);
        }
        if(!empty($accArr))
        {
            $res2 = $accessM->where('role_id='.$id)->delete();
        }
        if(!empty($r_uArr))
        {
            $res3 = $role_userM->where('role_id='.$id)->delete();
        }

        if($res1 && $res2 && $res3)
        {
            $roleM->commit();
            $this->success('角色删除成功',U('Rbac/roleList'));
        }else
        {
            $roleM->rollback();
            $this->error('角色删除失败');
        }
    }
    //添加节点/权限
    public function addNode()
    {
        $condition['level'] = array('neq','3');
        $sort['sort'] = 'asc';
        $this->node = M('Node')->where($condition)->order($sort)->select();
        $this->display();
    }

    //权限列表
    public function nodeList()
    {
        $tree = new Tree();
        $order['sort'] = 'asc';
        $node = M('Node')->order($order)->select();
        $this->nodeList = $tree->create($node);
        $this->display();
    }

    //添加节点表单处理
    public function do_addNode()
    {
        $nodeM = M('Node');
        $nodeM->create();
        if($nodeM->add())
        {
            $this->success('权限添加成功',U('Rbac/nodeList'));
        }else
        {
            $this->error('权限添加失败');
        }
    }

    //修改节点/权限
    public function editNode()
    {
        if(!isset($_GET['id']))
        {
            $this->error('参数丢失');
        }
        $id = I('get.id/d');
        $nodeM = M('Node');
        $this->nodeList = $nodeM->find($id);

        //父节点
        $where['level'] = array('neq','3');
        $sort['sort'] = 'asc';
        $this->pidnode = M('Node')->where($where)->order($sort)->select();

        $this->display();
    }

    //修改权限表单处理
    public function do_editNode()
    {
        if(!isset($_POST['id']) || empty($_POST['id']))
        {
            $this->error('权限id参数丢失');
        }
        $nodeM = M('Node');
        $nodeM->create();
        if($nodeM->save())
        {
            $this->success('权限修改成功',U('Rbac/nodeList'));
        }else
        {
            $this->error('权限修改失败');
        }
    }

    //权限删除
    public function delNode()
    {
        if(!isset($_GET['id']))
        {
            $this->error('请选择要删除的权限');
        }
        $id = I('get.id/d');

        //删除与权限有关联的表
        $nodeM = M('Node');
        $accessM = M('Access');

        $nodeArr = $nodeM->find($id);
        $accessArr = $accessM->where('node_id='.$id)->select();

        $res1 = 1;
        $res2 = 1;
        //开启事务处理
        $nodeM->startTrans();
        if(!empty($nodeArr))
        {
            $res1 = $nodeM->delete($id);
        }
        if(!empty($accessArr))
        {
            $res2 = $accessM->where('node_id='.$id)->delete();
        }

        if($res1 && $res2)
        {
            $nodeM->commit();
            $this->success('权限删除成功',U('Rbac/nodeList'));
        }else
        {
            $nodeM->rollback();
            $this->error('权限删除失败');
        }
    }

    //用户列表
    public function userList()
    {
        $where = array();
        //获取查询的条件
        if(isset($_GET['truename']) && !empty($_GET['truename']))
        {
            $where['truename'] = array('like','%'.$_GET['truename'].'%');
            $this->truename = I('get.truename');
        }else
        {
            $this->truename = '';
        }
        //获取查询的条件
        if(isset($_GET['phone']) && !empty($_GET['phone']))
        {
            $where['phone'] = array('like','%'.$_GET['phone'].'%');
            $this->phone = I('get.phone');
        }else
        {
            $this->phone = '';
        }
        //获取查询的条件
        if(isset($_GET['status']))
        {
            if(intval($_GET['status']) != 2)
            {
                $where['status'] = I('get.status/d');
            }
            $this->status = I('get.status/d');
        }else
        {
            $this->status = 2;
        }

        $userM = D('User');
        $count = $userM->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $userM->where($where)->relation(true)->order('logintime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('userList',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->total = $count;
        $this->display(); // 输出模板
    }

    //新增管理员
    public function addUser()
    {
        $this->role = M('Role')->select();
        $this->display();
    }

    //新增管理员表单处理
    public function do_addUser()
    {
        $res1 = 1;
        $res2 = 1;
        $userM = M('User');
        $userM->create();
        $userM->password = md5(I('post.password'));

        $userM->startTrans();
        $res1 = $userM->add();

        $data['role_id'] = I('post.role_id/d');
        $data['user_id'] = $res1;

        $res2 = M('roleUser')->add($data);

        if($res1 && $res2)
        {
            $userM->commit();
            $this->success('新用户添加成功',U('Rbac/userList'));
        }else
        {
            $userM->rollback();
            $this->error('新用户添加失败');
        }
    }

    //修改管理员
    public function editUser()
    {
        if(!isset($_GET['id']))
        {
            $this->error('请选择要修改的管理员');
        }
        $id = I('get.id/d');

        $userM = D('User');
        $this->userList = $userM->relation(true)->find($id);
        //角色信息
        $this->role = M('Role')->select();
        $this->display();
    }

    //修改管理员表单处理
    public function do_editUser()
    {
        if(!isset($_POST['id']) || empty($_POST['id']))
        {
            $this->error('用户id参数丢失');
        }
        $id = I('post.id/d');
        $userM = D('User');
        $userArr = $userM->relation(true)->find($id);

        $userM->create();

        if(!isset($_POST['password']) || empty($_POST['password']))
        {
            $userM->password = $userArr['password'];
        }else
        {
            $userM->password = md5(trim(I('post.password')));
        }

        $res1 = 1;
        $res2 = 1;
        $res3 = 1;

        $r_uM = M('role_user');

        $userM->startTrans();
        if($userArr['username'] != I('post.username') || $userArr['truename'] != I('post.truename') || $userArr['phone'] != I('post.phone') || $userArr['userimg'] != I('post.userimg') || intval($userArr['status']) != I('post.status/d') || !empty($_POST['password']))
        {
            $res1 = $userM->save();
        }

        if($userArr['Role'][0]['id'] != I('post.role_id/d'))
        {
            $where['user_id'] = $id;
            $where['role_id'] = $userArr['Role'][0]['id'];

            $r_uArr = $r_uM->where($where)->select();

            $data['user_id'] = $id;
            $data['role_id'] = I('post.role_id/d');
            if(!empty($r_uArr))
            {
                $res2 = $r_uM->where($where)->delete();
            }
            $res3 = $r_uM->add($data);
        }

        if($res1 && $res2 && $res3)
        {
            $userM->commit();
            $this->success('用户修改成功',U('Rbac/userList'));
        }else
        {
            $userM->rollback();
            $this->error('用户修改失败');
        }
    }

    //用户状态的开启、禁用和删除
    public function delUser()
    {
        if(!isset($_GET['id']))
        {
            $this->error('请选择要操作的管理员');
        }

        if(!isset($_GET['kd']))
        {
            $this->error('操作类型参数丢失');
        }

        $id = I('get.id/d');
        $kd = I('get.kd/d');

        $userM = M('User');
        $userArr = $userM->find($id);

        $data['status'] = $kd;
        $res1 = 1;
        $res2 = 1;
        $msg = '操作';

        $userM->startTrans();
        if($kd == 0 && $userArr['status'] != 0)
        {
            $msg = '禁用';
            $res1 = $userM->where('id='.$id)->save($data);
        }elseif($kd == 1 && $userArr['status'] != 1)
        {
            $msg = '启用';
            $res1 = $userM->where('id='.$id)->save($data);
        }elseif($kd == 2)
        {
            $r_uM = M('RoleUser');
            $msg = '删除';
            $res1 = $userM->delete($id);
            $c['user_id'] = $id;

            $u_rArr = $r_uM->where($c)->select();
            if(!empty($u_rArr))
            {
                $res2 = $r_uM->where($c)->delete();
            }
        }

        if($res1 && $res2)
        {
            $userM->commit();
            $this->success("用户".$msg."成功",U('Rbac/userList'));
        }else
        {
            $userM->rollback();
            $this->error("用户".$msg."失败");
        }
    }

    //权限配置
    public function access()
    {
        if(!isset($_GET['rid']) || I('get.rid/d')==0)
        {
            $this->error('操作的角色id不存在');
        }
        $rid = I('get.rid/d');
        $nodeM = M('Node');
        $node = $nodeM->order('sort')->select();

        $tree = new Tree();
        $node = $tree->create($node);

        $data = array();
        $accessM = M('Access');
        foreach($node as $value)//$data用于存放最新数组,里面包含当前用户组是否有每一个权限
        {
            $count = $accessM->where('role_id = '.$rid.' and node_id = '.$value['id'])->count();
            if($count)
            {
                $value['access'] = 1;
            }else
            {
                $value['access'] = 0;
            }
            $data[]=$value;
        }
        $this->nodeList = $data;
        $this->rid = $rid;
        $this->role_name = M('Role')->where('id = '.$rid)->getField('name');
        $this->display();
    }

    //权限配置表单处理
    public function doAccess()
    {
        if(!isset($_POST['role_id']) || I('post.role_id/d') == 0)
        {
            $this->error('权限配置角色id不存在');
        }
        $rid = I('post.role_id/d');
        $accessM = M('Access');
        $res1 = 1;
        $res2 = 1;

        $accessArr = $accessM->where('role_id = '.$rid)->select();

        $accessM->startTrans();
        //清空当前角色的所有权限
        if(!empty($accessArr))
        {
            $res1 = $accessM->where('role_id = '.$rid)->delete();
        }
        if(isset($_POST['access']))
        {
            $data = array();
            foreach($_POST['access'] as $value)
            {
                $tmp = explode('_',$value);
                $data[] = array(
                    'role_id' => $rid,
                    'node_id' => $tmp[0],
                    'level' => $tmp[1]
                );
            }
            $res2 = $accessM->addAll($data);
        }
        if($res1 && $res2)
        {
            $accessM->commit();
            $this->success('权限修改成功',U('Rbac/roleList'));
        }else
        {
            $accessM->rollback();
            $this->success('权限修改失败');
        }
    }

    //修改密码
    public function changePWD()
    {
        if(!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id']))
        {
            $this->error('登录异常',U('Index/index'));
        }

        $id = session('admin_id');
        $userM = M('User');
        $this->userArr = $userM->find($id);
        $this->display();
    }

    //密码修改表单处理
    public function do_changePWD()
    {
        if(!isset($_POST['id']) || empty($_POST['id']))
        {
            $this->error('登录异常,重新登录',U('Index/index'));
        }

        $id = I('post.id/d');
        $userM = M('User');
        $userArr = $userM->find($id);
        $res = 1;
        $userM->create();
        if(isset($_POST['password']) && !empty($_POST['password']) && md5(trim($_POST['password']))!= $userArr['password'])
        {
            $userM->password = md5(trim($_POST['password']));
            $res = $userM->save();
        }
        if($res)
        {
            $this->success('密码修改成功');
        }else
        {
            $this->error('密码修改失败');
        }
    }
}