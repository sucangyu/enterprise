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


<style type="text/css">
	th{text-align: center}
</style>
<div class="wrapper">
	<div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<!--<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>  -->
	</ol>
</div>

	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">
							<i class="fa fa-list"></i>&nbsp;权限管理
						</h3>
					</div>
					<div class="panel-body">
						<nav class="navbar navbar-default">
							<div class="collapse navbar-collapse">
								<form class="navbar-form form-inline" role="search">
									<div class="form-group pull-right">
										<a href="<?php echo U('Rbac/addNode');?>" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>添加权限</a>
									</div>
								</form>
							</div>
						</nav>
						<div class="table-responsive">
							<table  class="table table-bordered table-striped">
								<thead>
								<tr>
									<th>权限ID</th>
									<th>权限结构</th>
									<th>权限名称</th>
									<th>类型</th>
									<th>状态</th>
									<th>排序</th>
									<th>操作</th>
								</tr>
								</thead>
								<tbody>
								<?php if(is_array($nodeList)): $i = 0; $__LIST__ = $nodeList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr role="row" align="center">
										<td><?php echo ($vo["id"]); ?></td>
										<td>
											<p style="text-indent: <?php echo ($vo['level']*20); ?>px;"><b style="font-size: 22px;">.&nbsp;</b><?php echo ($vo["title"]); ?></p>
										</td>
										<td><?php echo ($vo["name"]); ?></td>
										<td>
											<?php if($vo["level"] == 1): ?>项目
												<?php elseif($vo["level"] == 2): ?>
												<span style="color: green">模块</span>
												<?php else: ?>
												<span style="color: blue">方法</span><?php endif; ?>
										</td>
										<td>
											<?php if($vo['status'] == 0): ?><space style="color: red">关闭</space>
												<?php else: ?>
												<space style="color: green">开启</space><?php endif; ?>
										</td>
										<td><?php echo ($vo["sort"]); ?></td>
										<td>
											<a class="btn btn-primary" href="<?php echo U('Rbac/editNode',array('id'=>$vo['id']));?>"><i class="fa fa-pencil">&nbsp;修&nbsp;改&nbsp;</i></a>
											<a class="btn btn-danger" href="<?php echo U('Rbac/delNode',array('id'=>$vo['id']));?>" onclick="return confirm('此操作不可恢复，确认删除？');"><i class="fa fa-trash-o">&nbsp;删&nbsp;除&nbsp;</i></a>
										</td>
									</tr><?php endforeach; endif; else: echo "" ;endif; ?>
								</tbody>
							</table>
						</div>
						<div class="row">
							<div class="col-sm-6 text-left"></div>
							<div class="col-sm-6 text-right"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
</body>
</html>