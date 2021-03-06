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
.panel-title{height: 33px;}
.fa-list{line-height: 33px}
.title{line-height: 33px}
#email{padding: 0 20px 20px 20px;display: none;}
#nullEmail{display: none;color: red;}
</style>
<div class="wrapper">
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<!--<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>  -->
	</ol>
</div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-list"></i> <span class="title">部门列表</span>
                        <button type="button" onclick="send()" class="btn btn-info pull-right"><i class="glyphicon glyphicon-envelope"></i>群发邮件</button>
                        <button type="button" onclick="location.href='<?php echo U('Department/addDepartment');?>'" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>添加新部门</button>
                    </h3>
                    
                </div>
                <div class="panel-body">
                    <div class="navbar navbar-default">
                        <form action="" id="search-form2" class="navbar-form form-inline" method="post" onsubmit="return false">
                            <div class="form-group">
                                <label class="control-label" for="input-titles">部门名称</label>
                                <div class="input-group">
                                    <input type="text" name="title" value="" placeholder="部门名称" id="input-titles" class="form-control">
                                    <!--<span class="input-group-addon" id="basic-addon2"><i class="fa fa-search"></i></span>-->
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-head">部门负责人</label>
                                <div class="input-group">
                                    <input type="text" name="head" value="" placeholder="部门负责人" id="input-head" class="form-control">
                                    <!--<span class="input-group-addon" id="basic-addon2"><i class="fa fa-search"></i></span>-->
                                </div>
                            </div>
                            <!-- <input type="hidden" name="order_by" value="id">
                            <input type="hidden" name="sort" value="desc"> -->
                            <button type="submit" onclick="ajax_get_table('search-form2',1)" id="button-filter search-order" class="btn btn-primary pull-right"><i class="fa fa-search">查找</i></button>
                        </form>
                    </div>
                    <div id="ajax_return">

                    </div>
                </div>
            </div>
        </div>        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<div id="email">
    <form method="post" id="sendEmail" action="/enterprise/index.php/Admin/Department/sendEmail">
        <div class="form-group">
            <label for="exampleInputEmail1">收件人</label>
            <input type="text" class="form-control" id="exampleInputEmail1" name="email" placeholder="Email">
        </div>
        <span id="nullEmail"></span>
        <div class="form-group">
            <label for="title">主题</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="邮件标题">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">内容</label>
            <textarea class="span12 ckeditor" style="width: 100%;height:30vh;" id="content" name="content" title=""></textarea>
        </div>
        <div class="form-group">
            <span></span>
            <span style="float: right;">发送者:<?php echo (session('admin_name')); ?></span>
        </div>
        <button type="submit" class="btn btn-success btn-block">发送</button>
    </form>
</div>
<!-- /.content-wrapper -->
<script>
    $(document).ready(function(){
        ajax_get_table('search-form2',1);

    });

    // ajax 抓取页面
    function ajax_get_table(tab,page){
        cur_page = page; //当前页面 保存为全局变量
        var url = "<?php echo U('Department/ajaxindex');?>";
        $.ajax({
            type : "POST",
            url:url+"?p="+page,//+tab,
            data : $('#'+tab).serialize(),// 你的formid
            success: function(data){
                //console.log(data);
                $("#ajax_return").html('');
                $("#ajax_return").append(data);
            }
        });
    }
    
</script>
</body>
</html>