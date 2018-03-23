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


<link rel="stylesheet" href="/enterprise/Public/kindeditor/themes/default/default.css" />
<script src="/enterprise/Public/jquery-2.1.4/jquery.min.js"></script>
<script src="/enterprise/Public/kindeditor/kindeditor-all.js"></script>
<script src="/enterprise/Public/kindeditor/lang/zh-CN.js"></script>
<script type="text/javascript">
    KindEditor.ready(function(K) {
        var editor = K.editor({
            allowFileManager : true
        });
        K('#image1').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    imageUrl : K('#url1').val(),
                    clickFn : function(url, title, width, height, border, align) {
                        K('#url1').val(url);
                        K('#show_img').html('<img style="width: 200px; height: 150px;" src="'+url+'"/>');
                        editor.hideDialog();
                    }
                });
            });
        });
    });
</script>

<div class="wrapper">
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<!--<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>  -->
	</ol>
</div>

    <section class="content" style="padding:0px 15px;">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-body ">
                    <ul class="nav nav-tabs">
                        <?php if(is_array($group_list)): foreach($group_list as $k=>$vo): ?><li <?php if($k == 'shop_info'): ?>class="active"<?php endif; ?>><a href="javascript:void(0)" data-url="<?php echo U('System/index',array('inc_type'=>$k));?>" data-toggle="tab" onclick="goset(this)"><?php echo ($vo); ?></a></li><?php endforeach; endif; ?>
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="handlepost" action="<?php echo U('System/handle');?>">
                        <!--通用信息-->
                        <div class="tab-content" style="padding:20px 0px;">
                            <div class="tab-pane active" id="tab_tongyong">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td class="col-sm-2">网站备案号：</td>
                                        <td class="col-sm-8">
                                            <input type="text" class="form-control" name="record_no" value="<?php echo ($config["record_no"]); ?>" >
                                            <span id="err_attr_ba" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2">网站名称：</td>
                                        <td class="col-sm-8">
                                            <input type="text" class="form-control" name="store_name" value="<?php echo ($config["store_name"]); ?>" >
                                            <span id="err_attr_name" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>网站logo：</td>
                                        <td>

                                            <div id="show_img">
                                                <?php if(!empty($config['store_logo'])): ?><img style="width: 200px; height: 150px;" src="<?php echo ($config["store_logo"]); ?>"/><?php endif; ?>
                                            </div>
                                            <div>
                                                <input type="hidden" id="url1" name="store_logo" value="<?php echo ($config["store_logo"]); ?>" />
                                                <input type="button" id="image1" value="选择图片" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>网站标题：</td>
                                        <td >
                                            <input type="text" class="form-control" name="store_title" value="<?php echo ($config["store_title"]); ?>" >
                                            <span id="err_type_id" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>网站描述：</td>
                                        <td>
                                            <input type="text" class="form-control" name="store_desc" value="<?php echo ($config["store_desc"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>网站关键字：</td>
                                        <td>
                                            <input type="text" class="form-control" name="store_keyword" value="<?php echo ($config["store_keyword"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>联系人：</td>
                                        <td>
                                            <input type="text" class="form-control" name="contact" value="<?php echo ($config["contact"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>联系电话：</td>
                                        <td>
                                            <input type="text" class="form-control" name="phone" value="<?php echo ($config["phone"]); ?>" >
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>具体地址：</td>
                                        <td>
                                            <input type="text" class="form-control" name="address" value="<?php echo ($config["address"]); ?>" >
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>客服QQ1：</td>
                                        <td>
                                            <input type="text" class="form-control" name="qq" value="<?php echo ($config["qq"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>客服QQ2：</td>
                                        <td>
                                            <input type="text" class="form-control" name="qq2" value="<?php echo ($config["qq2"]); ?>" >
                                        </td>
                                    </tr>
                                    <!--<tr>
                                        <td>客服QQ3：</td>
                                        <td>
                                            <input type="text" class="form-control" name="qq3" value="<?php echo ($config["qq3"]); ?>" >
                                        </td>
                                    </tr>-->
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td><input type="hidden" name="inc_type" value="<?php echo ($inc_type); ?>"></td>
                                        <td class="text-right"><input class="btn btn-primary" type="button" onclick="adsubmit()" value="保存"></td></tr>
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
        /*
         var site_url = $('input[name="site_url"]').val();
         var urlReg = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\w \.-]*)*$/;
         if(!urlReg.exec(site_url))
         {
         alert('网站域名格式必须是 http://www.xxx.com');
         return false;
         }
         */
        $('#handlepost').submit();
    }

    $(document).ready(function(){
//        get_province();
    });

    function goset(obj){
        window.location.href = $(obj).attr('data-url');
    }
</script>
</body>
</html>