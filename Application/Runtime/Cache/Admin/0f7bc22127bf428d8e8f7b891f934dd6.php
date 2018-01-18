<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo ($store_name); ?>管理后台</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.4 -->
	<link href="/enterprise/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- FontAwesome 4.3.0 -->
	<link href="/enterprise/Public/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<!-- Ionicons 2.0.0 --
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
	<link href="/enterprise/Public/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
	<!-- AdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
	<link href="/enterprise/Public/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
	<!-- iCheck -->
	<link href="/enterprise/Public/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />
	<!-- jQuery 2.1.4 -->

	<script src="/enterprise/Public/jquery-2.1.4/jquery.min.js"></script>
	<script src="/enterprise/Public/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="/enterprise/Public/layer/layer-min.js"></script><!-- 弹窗js 参考文档 http://layer.layui.com/-->
	<script src="/enterprise/Public/js/myAjax.js"></script>
	<script type="text/javascript">
		function delfunc(obj){
			layer.confirm('确认删除？', {
						btn: ['确定','取消'] //按钮
					}, function(){
						// 确定
						$.ajax({
							type : 'post',
							url : $(obj).attr('data-url'),
							data : {act:'del',del_id:$(obj).attr('data-id')},
							dataType : 'json',
							success : function(data){
								if(data==1){
									layer.msg('操作成功', {icon: 1});
									$(obj).parent().parent().remove();
								}else{
									layer.msg(data, {icon: 2,time: 2000});
								}
								layer.closeAll();
							}
						})
					}, function(index){
						layer.close(index);
						return false;// 取消
					}
			);
		}

		function selectAll(name,obj){
			$('input[name*='+name+']').prop('checked', $(obj).checked);
		}
	</script>
	<link rel="stylesheet" href="/enterprise/Public/css/admin_css.css" />

</head>
<body style="background-color:#ecf0f5;">


<div class="wrapper">
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<!--<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>  -->
	</ol>
</div>

    <section class="content ">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回权限列表"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 添加权限</h3>
                </div>
                <div class="panel-body ">
                    <!--表单数据-->
                    <form method="post" id="adminHandle" action="<?php echo U('Rbac/do_addNode');?>" onsubmit="return adsubmit();">
                        <!--通用信息-->
                        <div class="tab-content col-md-10">
                            <div class="tab-pane active" id="tab_tongyong">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td class="col-sm-2">英文名称：</td>
                                        <td class="col-sm-8">
                                            <input type="text" class="form-control" name="name" value="" >

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>中文名称：</td>
                                        <td >
                                            <input type="text" class="form-control" name="title" value="" >

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>状态：</td>
                                        <td>
                                            <select name="status">
                                                <option value="1" selected >开启</option>
                                                <option value="0">关闭</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>类型：</td>
                                        <td>
                                            <select name="level">
                                                <option value="1" selected >项目</option>
                                                <option value="2">模块</option>
                                                <option value="3">方法</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>父节点：</td>
                                        <td>
                                            <select name="pid">
                                                <option value="0" selected>根节点</option>
                                                <?php if(is_array($node)): $i = 0; $__LIST__ = $node;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($vo["level"]) == "1"): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?></option>
                                                        <?php else: ?>
                                                        <option value="<?php echo ($vo["id"]); ?>">　｜<?php echo ($vo["title"]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>排序：</td>
                                        <td >
                                            <input type="text" class="form-control" name="sort" value="0" >
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td>
                                        </td>
                                        <td class="text-right">
                                            <input class="btn btn-primary" type="submit" value="添加">
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </form><!--表单数据-->
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    function adsubmit(){
        if($('input[name=name]').val() == ''){
            layer.msg('权限名不能为空！', {icon: 2,time: 1000});   //alert('少年，用户名不能为空！');
            $('input[name=name]').focus();
            return false;
        }else if($('input[name=title]').val() == ''){
            layer.msg('权限名称不能为空！', {icon: 2,time: 1000});//alert('少年，邮箱不能为空！');
            $('input[name=title]').focus();
            return false;
        }else
        {
            return true;
        }
    }
</script>
</body>
</html>