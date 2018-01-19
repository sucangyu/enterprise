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
    .rad_2{border:1px solid #ccc;padding:7px 16px;box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);border-radius:5px;}
    input[name=kind]:checked + .rad_2, .rad_2.checked {
        background:#ccc
    }
</style>
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
        K.create('textarea[name="desc"]', {
            allowFileManager : true,
            //ajax提交表单时获取tesxarea值
            afterBlur: function(){this.sync();}
        });

        K('#original_img').click(function() {
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


        K('#pics').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    clickFn : function(url, title, width, height, border, align) {
                        var div2 = $('#show_pics');
                        var html = '<div style="width:100px; text-align:center; margin: 5px; display:inline-block;float:left;" class="goods_xc">';
                        html += '<input type="hidden" value="'+url+'" name="pics[]">';
                        html += '<a><img width="100" height="100" src="'+url+'"></a><br>';
                        html += '<a href="javascript:void(0)" onclick="ClearPicArr2(this)">删除</a></div>';
                        div2.append(html);
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
                    <h3 class="panel-title"><i class="fa fa-list"></i>商品详情</h3>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_tongyong" data-toggle="tab">通用信息</a></li>
                        <!-- <li><a href="#tab_goods" data-toggle="tab" id="joink">收成信息</a></li> -->
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="addEditGoods" action="/enterprise/index.php/Admin/Goods/addEditGoods">

                        <!--通用信息-->
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_tongyong">

                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>商品名称:<i style="color: red;">*</i></td>
                                        <td>
                                            <input type="text" value="<?php echo ($goodsInfo["titles"]); ?>" name="goods_name" class="form-control" style="width:350px;"/>
                                            <span id="err_titles" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品货号:<i style="color: red;">*</i></td>
                                        <td>
                                            <input type="text" value="<?php echo ($goodsInfo["goods_sn"]); ?>" name="goods_sn" class="form-control" style="width:350px;"/>
                                            <span id="err_goods_sn" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品分类:</td>
                                        <td>
                                            <select class="form-control" name="cat_id" style="width:350px;">
                                                <?php if(is_array($cationArr)): foreach($cationArr as $key=>$vo1): ?><option value="<?php echo ($vo1["id"]); ?>"><?php echo ($vo1["title"]); ?></option><?php endforeach; endif; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品简介:<i style="color: red;">*</i></td>
                                        <td>
                                            <textarea rows="3" cols="80" name="goods_remark"><?php echo ($goodsInfo["goods_remark"]); ?></textarea>
                                            <span id="err_goods_remark" style="color:#F00; display:none;"></span>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品关键词:</td>
                                        <td>
                                            <input type="text" class="form-control" style="width:550px;" value="<?php echo ($goodsInfo["keywords"]); ?>" name="keywords"/>用空格分隔
                                            <span id="err_keywords" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>是否推荐</td>
                                        <td>
                                            <select name="stats" style="width:150px;height:30px;">
                                                <option value="0">推荐</option>
                                                <option value="1">不推荐</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>上传商品轮播图片:<i style="color: red;">*</i></td>
                                        <td>
                                            <div id="show_pics">
                                                <?php if(is_array($projArr['projimages'])): foreach($projArr['projimages'] as $k=>$vo): ?><div style="width:100px; text-align:center; margin: 5px; display:inline-block;float: left;" class="goods_xc">
                                                        <input type="hidden" value="<?php echo ($vo); ?>" name="pics[]">
                                                        <a onclick="" href="<?php echo ($vo); ?>" target="_blank"><img width="100" height="100" src="<?php echo ($vo); ?>"></a>
                                                        <br>
                                                        <a href="javascript:void(0)" onclick="ClearPicArr2(this)">删除</a>
                                                    </div><?php endforeach; endif; ?>
                                            </div>
                                            <div class="goods_xc" style="width:100px; text-align:center; margin: 5px; display:inline-block;">
                                                <a href="javascript:void(0);" id="pics"> <img src="/enterprise/Public/imgs/add-button.jpg" width="100" height="100" /></a>
                                                <br/>
                                                <a href="javascript:void(0)">&nbsp;&nbsp;</a>
                                            </div>
                                            <span id="err_pics" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>市场价格:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($goodsInfo["market_price"]); ?>" name="tmoney" class="form-control" style="width:350px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                            <span id="err_tmoney" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>本店价格:<i style="color: red;">*</i></td>
                                        <td>
                                            <input type="text" value="<?php echo ($goodsInfo["shop_price"]); ?>" name="shop_price" class="form-control" style="width:350px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                            <span id="err_shop_price" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品成本:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($goodsInfo["cost_price"]); ?>" name="cost_price" class="form-control" style="width:350px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                            <span id="err_omoney" style="color:#F00; display:none"></span>
                                        </td>
                                    </tr>
                                    </tr>
                                    <tr id="store_nums">
                                        <td>库存数量:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($goodsInfo["nums"]); ?>" name="nums" class="form-control" style="width:350px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                            <span id="err_store_count" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品重量:</td>
                                        <td>
                                            <input type="text" class="form-control" style="width:150px;" value="<?php echo ($goodsInfo["weight"]); ?>" name="weight" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                            &nbsp;克 (以克为单位)
                                            <span id="err_weight" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品详情描述:<i style="color: red;">*</i></td>
                                        <td width="85%">
                                            <textarea class="span12 ckeditor" style="width: 700px;height:500px;" id="good_content" name="desc" title=""></textarea>
                                            <span id="err_desc" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--其他信息-->



                        </div>
                        <div class="pull-right">
                            <input type="hidden" name="good_id" value="">
                            <button class="btn btn-primary" title="" data-toggle="tooltip" type="button" data-original-title="保存" id="bao">保存</button>
                        </div>
                    </form><!--表单数据-->
                </div>
            </div>
        </div>    <!-- /.content -->
    </section>
</div>
<script>
    $("input[name='kind']").click(function(){
        if($(this).val() == 1){
            $("#joink").css("display","none")
        }else{
            $("#joink").css("display","");
            $("#store_nums").css("display","none");
        }
    });

    $("#bao").click(function(){
        // alert('ssss');
        var goods_name = $("input[name='goods_name']").val();
        var goods_sn = $("input[name='goods_sn']").val();
        var pics = $("input[name='pics[]']").val();//轮播图
        var shop_price = $("input[name='shop_price']").val();//本店价
        var desc = $("#good_content").val();//详情
        var goods_remark = $("textarea[name='goods_remark']").val();//简介

        var error = '';
        if (goods_name==0) {
            error += '必须添加商品名称,   ';
            $("#err_titles").html('必须添加商品名称');
            $("#err_titles").css('display','block');
        }
        if (goods_sn==0) {
            error += '必须添加商品货号,   ';
            $("#err_goods_sn").html('必须添加商品货号');
            $("#err_goods_sn").css('display','block');
        }
        if (pics==undefined) {
            error += '必须添加商品轮播图片,   ';
            $("#err_pics").html('必须上传项目轮播图片');
            $("#err_pics").css('display','block');
        }
        if (shop_price==0) {
            error += '必修添加本店价格,   ';
            $("#err_shop_price").html('必修添加本店价格');
            $("#err_shop_price").css('display','block');
        }
        if (goods_remark==0) {
            error += '必须添加商品简介,   ';
            $("#err_goods_remark").html('必须添加商品简介');
            $("#err_goods_remark").css('display','block');
        }
        if (desc==0) {
            error += '必须添加商品详情,   ';
            $("#err_desc").html('必须添加商品详情');
            $("#err_desc").css('display','block');
        }
        if(error){
            alert(error);
            return false;
        }else{
            $("#addEditGoods").submit();
        }

    })

    /*
     * 以下是图片上传方法
     */
    // 上传商品图片成功回调函数
    function call_back(fileurl_tmp){
        $("#original_img").val(fileurl_tmp);
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