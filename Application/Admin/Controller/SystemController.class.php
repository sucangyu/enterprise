<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Use:设置管理
 * Date: 2016/6/12
 * Time: 11:27
 */

namespace Admin\Controller;
use Think\Controller;
class SystemController extends CommonController
{
	/*
	 * 配置入口
	 */
	public function index()
	{
		/*配置列表*/
		$group_list = array('shop_info'=>'网站信息');
		$this->assign('group_list',$group_list);
		$inc_type =  I('get.inc_type','shop_info');
		$this->assign('inc_type',$inc_type);
		$this->assign('config',dsCache($inc_type));//当前配置项
		C('TOKEN_ON',false);
		$this->display($inc_type);
	}

	/*
	 * 新增修改配置
	 */
	public function handle()
	{
		$param = I('post.');
		$inc_type = $param['inc_type'];
		//unset($param['__hash__']);
		unset($param['inc_type']);
		tpCache($inc_type,$param);
		$this->success("操作成功",U('System/index',array('inc_type'=>$inc_type)));
	}

	/**
	 * 清空系统缓存
	 */
	public function cleanCache(){
		//$img_arr = glob('./Public/upload/goods/thumb/*'); //$aa = scandir('./Public/upload/goods/thumb/');
		//foreach($img_arr as $key => $val)
		//   unlink ($val);// 删除缩略图
		delFile('./Public/upload/goods/thumb');// 删除缩略图
		if(delFile(RUNTIME_PATH))// 删除缓存 删除 \Application\Runtime 下面的所有文件
			$this->success("清除成功!!!");
		else
			$this->error("操作完成!!");
	}

	//数据库备份
	public function backupMysql() {
        $DataDir = "databak/";
        mkdir($DataDir);
        if (!empty($_GET['Action'])) {
            import("Common.Org.MySQLReback");
            $config = array(
                'host' => C('DB_HOST'),
                'port' => C('DB_PORT'),
                'userName' => C('DB_USER'),
                'userPassword' => C('DB_PWD'),
                'dbprefix' => C('DB_PREFIX'),
                'charset' => 'UTF8',
                'path' => $DataDir,
                'isCompress' => 0, //是否开启gzip压缩
                'isDownload' => 0
            );
            $mr = new MySQLReback($config);
            $mr->setDBName(C('DB_NAME'));
            if ($_GET['Action'] == 'backup') {
                $mr->backup();
                echo "<script>document.location.href='" . U("System/backupMysql") . "'</script>";
				// $this->success( '数据库备份成功！');
            } elseif ($_GET['Action'] == 'RL') {
                $mr->recover($_GET['File']);
                echo "<script>document.location.href='" . U("System/backupMysql") . "'</script>";
				// $this->success( '数据库还原成功！');
            } elseif ($_GET['Action'] == 'Del') {
                if (@unlink($DataDir . $_GET['File'])) {
                    // $this->success('删除成功！');
                    echo "<script>document.location.href='" . U("System/backupMysql") . "'</script>";
                } else {
                    $this->error('删除失败！');
                }
            }
            if ($_GET['Action'] == 'download') {

                function DownloadFile($fileName) {
                    ob_end_clean();
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Length: ' . filesize($fileName));
                    header('Content-Disposition: attachment; filename=' . basename($fileName));
                    readfile($fileName);
                }
                DownloadFile($DataDir . $_GET['file']);
                exit();
            }
        }
        
        $this->assign("datadir",$DataDir);
        $this->display();
    }

    public function ajaxBackupMysql(){
    	$DataDir = "databak/";
        mkdir($DataDir);
    	$lists = $this->MyScandir('databak/');
    	$this->assign("lists", $lists);
    	$this->assign("datadir",$DataDir);
        $this->display();
    }

    private function MyScandir($FilePath = './', $Order = 0) {
        $FilePath = opendir($FilePath);
        while (false !== ($filename = readdir($FilePath))) {
            $FileAndFolderAyy[] = $filename;
        }
        $Order == 0 ? sort($FileAndFolderAyy) : rsort($FileAndFolderAyy);
        return $FileAndFolderAyy;
    }
}