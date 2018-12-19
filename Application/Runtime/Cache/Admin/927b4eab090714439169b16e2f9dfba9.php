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


<!--物流配置 css -start-->
<style>
    ul.group-list {
        width: 96%;min-width: 1000px; margin: auto 5px;list-style: disc outside none;
    }
    ul.group-list li {
        white-space: nowrap;float: left;
        width: 150px; height: 25px;
        padding: 3px 5px;list-style-type: none;
        list-style-position: outside;border: 0px;margin: 0px;
    }
</style>
<!--物流配置 css -end-->
<link rel="stylesheet" href="/enterprise/Public/kindeditor/themes/default/default.css" />
<script src="/enterprise/Public/kindeditor/kindeditor-all.js"></script>
<script src="/enterprise/Public/kindeditor/lang/zh-CN.js"></script>
<script type="text/javascript">
    /*
     * 上传之后删除组图input
     * @access   public
     * @val      string  删除的图片input
     */
    function ClearPicArr2(obj)
    {
        $(obj).parent().remove(); // 删除完服务器的, 再删除 html上的图片
    }

    KindEditor.ready(function(K) {
        var editor = K.editor({
            allowFileManager : false
        });
        K.create('textarea[name="content1"]', {
            allowFileManager : true,
            //ajax提交表单时获取tesxarea值
            afterBlur: function(){this.sync();}
        });

        K('#dep_img').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    imageUrl : K('#original_img_save').val(),
                    clickFn : function(url, title, width, height, border, align) {
                        K('#original_img_save').val(url);
                        K('#show_img').html('<img style="width: 200px; height: 150px;" src="'+url+'"/>');
                        editor.hideDialog();
                    }
                });
            });
        });

    });
</script>

<!--以上是在线编辑器 代码  end-->
<div class="wrapper">
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<!--<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>  -->
	</ol>
</div>

    <section class="content">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
                <!--<a href="javascript:;" class="btn btn-default" data-url="http://www.tp-shop.cn/Doc/Index/article/id/1007/developer/user.html" onclick="get_help(this)"><i class="fa fa-question-circle"></i> 帮助</a>-->
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i>部门详情</h3>
                </div>
                <div class="panel-body">
                    <!--表单数据-->
                    <form method="post" id="addEditProjForm" action="/enterprise/index.php/Admin/Department/detail">

                        <!--通用信息-->
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_tongyong">

                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>部门名称:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($deparArr["title"]); ?>" name="title" class="form-control" style="width:550px;" max="50"/>
                                            <span id="err_title" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>负责人:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($deparArr["head"]); ?>" name="head" class="form-control" style="width:550px;" max="50"/>
                                            <span id="err_head" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>部门图片:</td>
                                        <td>
                                            <div id="show_img">
                                                <?php if(empty($deparArr['dep_img'])): ?><img width="50" height="50" src="/enterprise/Public/imgs/add-button.jpg">
                                                <?php else: ?> 
                                                    <img width="200" height="150" src="<?php echo ($deparArr["dep_img"]); ?>"><?php endif; ?> 
                                            </div>
                                            <div>
                                                <input type="hidden" id="original_img_save" name="dep_img" value="<?php echo ($deparArr["dep_img"]); ?>" />
                                                <input type="button" id="dep_img" value="上传部门图片" />
                                            </div>
                                            <span id="err_dep_img" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>部门介绍:</td>
                                        <td width="85%">
                                            <!-- <textarea class="span12 ckeditor" style="width: 700px;height:500px;" id="content" name="content1" title=""><?php echo ($deparArr["content"]); ?></textarea> -->
                                            <textarea class="span12 ckeditor" style="width: 700px;height:200px;" id="content" name="content" title=""><?php echo ($deparArr["content"]); ?></textarea>
                                            <span id="err_content" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--其他信息-->
                        </div>
                        <div class="pull-left">
                            <input type="hidden" name="id" value="<?php echo ($deparArr["id"]); ?>">
                            <button class="btn btn-info" title="" data-toggle="tooltip" type="button" data-original-title="保存" id="bao"><i class="ace-icon fa fa-check bigger-110"></i>保存</button>
                        </div>
                    </form><!--表单数据-->
                </div>
            </div>
        </div>    <!-- /.content -->
    </section>
</div>
<script>
    $("#bao").click(function(){
        $(".error").css('display','none');
        // alert('ssss');
        var title = $("input[name='title']").val();
        var content = $("textarea[name='content']").val();
        var head = $("input[name='head']").val();
        var dep_img = $("input[name='dep_img']").val();
        // alert(content);
        var error = '';
        if (title==0) {
            error += '必须添加部门名称,   ';
            $("#err_title").html('必须添加部门名称');
            $("#err_title").css('display','block');
        }
        if (head==0) {
            error += '必须添加部门负责人,   ';
            $("#err_head").html('必须添加部门负责人');
            $("#err_head").css('display','block');
        }
        if (dep_img==0) {
            error += '必须上传部门图片,   ';
            $("#err_dep_img").html('必须上传部门图片');
            $("#err_dep_img").css('display','block');
        }
        if (content==0) {
            error += '必须添加部门介绍,   ';
            $("#err_content").html('必须添加部门介绍');
            $("#err_content").css('display','block');
        }

        if(error){
            alert(error);
            return false;
        }else{
            $("#addEditProjForm").submit();
        }
//

    })

    /*
     * 以下是图片上传方法
     */
    // 上传商品图片成功回调函数
    function call_back(fileurl_tmp){
        $("#original_img").val(fileurl_tmp);
        $("#original_video").val(fileurl_tmp);
        $("#original_img2").attr('href', fileurl_tmp);
    }




    // 属性输入框的加减事件
    function addAttr(a)
    {
        var attr = $(a).parent().parent().prop("outerHTML");
        attr = attr.replace('addAttr','delAttr').replace('+','-');
        $(a).parent().parent().after(attr);
    }
    // 属性输入框的加减事件
    function delAttr(a)
    {
        $(a).parent().parent().remove();
    }

</script>
</body>
</html>