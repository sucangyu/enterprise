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

    function ClearPicArr1(obj)
    {
        $(obj).parent().remove(); // 删除电话
    }
    KindEditor.ready(function(K) {
        var editor = K.editor({
            allowFileManager : false,

        });
        K.create('textarea[name="comp_content"]', {
            allowFileManager : true,
            //ajax提交表单时获取tesxarea值
            afterBlur: function(){this.sync();}
        });

        K('#log_img').click(function() {
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

        K('#original_video').click(function() {
            editor.loadPlugin('media', function() {
                editor.plugin.media.edit({

                });

            });

        });


        K('#pics').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    clickFn : function(url, title, width, height, border, align) {
                        var div2 = $('#show_pics');
                        var html = '<div style="width:100px; text-align:center; margin: 5px; display:inline-block;float:left;" class="goods_xc">';
                        html += '<input type="hidden" value="'+url+'" name="comp_images[]">';
                        html += '<a><img width="100" height="100" src="'+url+'"></a><br>';
                        html += '<a href="javascript:void(0)" onclick="ClearPicArr2(this)">删除</a></div>';
                        div2.append(html);
                        editor.hideDialog();
                    }
                });
            });
        });

        K('#phone').click(function() {

            var div2 = $('#show_phone');
            var html = '<div style="width:150px; text-align:center; margin: 5px; display:inline-block;float:left;" class="goods_xc">';
            html += '<input type="text" value="" name="comp_phone[]" maxlength="20" class="form-control" style="width:150px;"><br>';
            html += '<a href="javascript:void(0)" onclick="ClearPicArr1(this)">删除</a></div>';
            div2.append(html);
            editor.hideDialog();
                    
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
                    <h3 class="panel-title"><i class="fa fa-list"></i>公司简介</h3>
                </div>
                <div class="panel-body">
                    <!--表单数据-->
                    <form method="post" id="addEditProjForm" action="/enterprise/index.php/Admin/Company/Index">

                        <!--通用信息-->
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_tongyong">

                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>公司名称:<i style="color: red;">*</i></td>
                                        <td>
                                            <input type="text" value="<?php echo ($enprArr["name"]); ?>" name="comp_name" class="form-control" style="width:550px;"/>
                                            <span id="err_comp_name" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>公司简介:<i style="color: red;">*</i><span style="color: #eee;font-size: 12px;">(最多输入200字)</span></td>
                                        <td>
                                            <textarea rows="3" cols="80" maxlength="200" name="comp_desc"><?php echo ($enprArr["desc"]); ?></textarea>
                                            <span id="err_comp_desc" class="error" style="color:#F00; display:none;"></span>

                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td>上传公司Log图片:<i style="color: red;">*</i><span style="color: #eee">(尺寸30*30)</span></td>
                                        <td>
                                            <div id="show_img">
                                                <?php if(empty($enprArr['log_img'])): ?><img width="50" height="50" src="/enterprise/Public/imgs/add-button.jpg">
                                                <?php else: ?> 
                                                    <img width="200" height="150" src="<?php echo ($enprArr['log_img']); ?>"><?php endif; ?> 
                                            </div>
                                            <div>
                                                <input type="hidden" id="original_img_save" name="log_img" value="<?php echo ($enprArr['log_img']); ?>" />
                                                <input type="button" id="log_img" value="上传公司Log图片" />
                                            </div>
                                            <span id="err_comp_logimg" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>公司电话:<i style="color: red;">*</i></td>
                                        <td>
                                            <div id="show_phone">
                                                <?php if(is_array($enprArr['phone'])): foreach($enprArr['phone'] as $i=>$vo1): ?><div style="width:150px; text-align:center; margin: 5px; display:inline-block;float: left;" class="goods_xc">
                                                        <input type="text" value="<?php echo ($vo1); ?>" name="comp_phone[]" maxlength="20" class="form-control" style="width:150px;"/>
                                                        <br>
                                                        <a href="javascript:void(0)" onclick="ClearPicArr1(this)">删除</a>
                                                    </div><?php endforeach; endif; ?>
                                            </div>
                                            <div class="goods_xc" style="width:100px; text-align:center; margin: 5px; display:inline-block;">
                                                <!-- <input type="hidden" name="comp_images[]" value="" /> -->
                                                <a href="javascript:void(0);" id="phone"> <img src="/enterprise/Public/imgs/add-button.jpg" width="100" height="100" /></a>
                                                <br/>
                                                <a href="javascript:void(0)">&nbsp;&nbsp;</a>
                                            </div>
                                            <span id="err_comp_phone" class="error" style="color:#F00; display:none;"></span>

                                            <!-- <input type="text" value="<?php echo ($enprArr["phone"]); ?>" name="comp_phone" maxlength="20" class="form-control" style="width:150px;"/> -->
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>邮政编码:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($enprArr["zipcode"]); ?>" name="comp_zipcode" maxlength="10" class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                            <span id="err_comp_zipcode" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>公司传真:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($enprArr["fax"]); ?>" name="comp_fax" maxlength="10" class="form-control" style="width:150px;" />
                                            <span id="err_comp_fax" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>公司邮箱:<i style="color: red;">*</i></td>
                                        <td>
                                            <input type="text" value="<?php echo ($enprArr["email"]); ?>" name="comp_email" maxlength="50" class="form-control" style="width:150px;"  />
                                            <span id="err_comp_email" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>公司QQ:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($enprArr["qq"]); ?>" name="comp_QQ" maxlength="30" class="form-control" style="width:150px;" />
                                            <span id="err_comp_QQ" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>公司地址:<i style="color: red;">*</i></td>
                                        <td>
                                            省:                                            
                                            <select class="form-control" style="width: 150px;" id="pre" name="pre" onchange="chg(this);">
                                                <option value="-1">请选择</option>
                                                <?php if(is_array($reg)): foreach($reg as $i=>$re): if($enprArr['province'] == $re['id']): ?><option value="<?php echo ($re["id"]); ?>" selected><?php echo ($re["name"]); ?></option>
                                                    <?php else: ?>
                                                        <option value="<?php echo ($re["id"]); ?>"><?php echo ($re["name"]); ?></option><?php endif; endforeach; endif; ?>
                                            </select>
                                            市：
                                            <select class="form-control" style="width: 150px;" id="city" name="city" onchange="chg2(this)" ;>
                                                <option value="-1">请选择</option>
                                            </select>
                                            区：
                                            <select class="form-control" style="width: 150px;" id="area" name="area">
                                                <option value="-1">请选择</option>
                                            </select>
                                            详细地址:
                                            <input type="text" name="detailed" maxlength="100" style="width: 30vw;" placeholder="详细地址，街道，楼牌号" value="<?php echo ($enprArr["address"]); ?>" />
                                            <span id="err_area" class="error" style="color:#F00; display:none;"></span>
                                            <span id="err_detailed" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>上传首页轮播图片:<i style="color: red;">*</i></td>
                                        <td>
                                            <div id="show_pics">
                                                <?php if(is_array($shufflingArr)): foreach($shufflingArr as $k=>$vo): ?><div style="width:100px; text-align:center; margin: 5px; display:inline-block;float: left;" class="goods_xc">
                                                        <input type="hidden" value="<?php echo ($vo["img_src"]); ?>" name="comp_images[]">
                                                        <a onclick="" href="<?php echo ($vo); ?>" target="_blank"><img width="100" height="100" src="<?php echo ($vo["img_src"]); ?>"></a>
                                                        <br>
                                                        <a href="javascript:void(0)" onclick="ClearPicArr2(this)">删除</a>
                                                    </div><?php endforeach; endif; ?>
                                            </div>
                                            <div class="goods_xc" style="width:100px; text-align:center; margin: 5px; display:inline-block;">
                                                <!-- <input type="hidden" name="comp_images[]" value="" /> -->
                                                <a href="javascript:void(0);" id="pics"> <img src="/enterprise/Public/imgs/add-button.jpg" width="100" height="100" /></a>
                                                <br/>
                                                <a href="javascript:void(0)">&nbsp;&nbsp;</a>
                                            </div>
                                            <span id="err_comp_images" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>友情链接:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($enprArr["link"]); ?>" name="comp_link" class="form-control" style="width:550px;" max="50"/>
                                            <span id="err_comp_link" class="error" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>公司详情描述:<i style="color: red;">*</i></td>
                                        <td width="85%">
                                            <textarea class="span12 ckeditor" style="width: 700px;height:500px;" id="comp_content" name="comp_content" title=""><?php echo ($enprArr["details"]); ?></textarea>
                                            <span id="err_comp_content" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--其他信息-->
                        </div>
                        <div class="pull-left">
                            <input type="hidden" name="comp_id" value="<?php echo ($enprArr["id"]); ?>">
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

        var comp_name = $("input[name='comp_name']").val();//公司名称
        var comp_desc = $("textarea[name='comp_desc']").val();//公司简介
        var comp_phone = $("input[name='comp_phone[]']").val();//电话
        var comp_email = $("input[name='comp_email']").val();//邮箱
        var log_img = $("input[name='log_img']").val();//log图
        var area = $("#area").val();//区
        var detailed = $("input[name='detailed']").val();//详细地址
        var comp_images = $("input[name='comp_images[]']").val();//轮播图
        var comp_content = $("#comp_content").val();//详情介绍

        //var proj_price = $("input[name='comp_price']").val();
        var error = '';
        //alert(comp_name+","+comp_desc+","+comp_phone+","+comp_email+","+log_img+","+area+","+detailed+","+comp_images+","+comp_content);

        $(".error").css('display','none');
        if (!comp_name) {
            error += '必须添加公司名称,   ';
            $("#err_comp_name").html('必须添加公司名称');
            $("#err_comp_name").css('display','block');
        }
        if (!comp_desc) {
            error += '必须添加公司简介,   ';
            $("#err_comp_desc").html('必须添加公司简介');
            $("#err_comp_desc").css('display','block');
        }
        if (!comp_images) {
            error += '必须上传首页轮播图片,   ';
            $("#err_comp_images").html('必须上传首页轮播图片');
            $("#err_comp_images").css('display','block');
        }
        if (!comp_phone) {
            error += '必须添加公司电话,   ';
            $("#err_comp_phone").html('必须添加公司电话');
            $("#err_comp_phone").css('display','block');
        }
        if (!log_img) {
            error += '必须添加公司log图,   ';
            $("#err_log_img").html('必须添加公司log图');
            $("#err_log_img").css('display','block');
        }
        if (!comp_email) {
            error += '必须添加公司邮箱,   ';
            $("#err_comp_email").html('必须添加公司邮箱');
            $("#err_comp_email").css('display','block');
        }
        if (area==-1) {
            error += '必须添加公司地址,   ';
            $("#err_area").html('必须添加公司地址');
            $("#err_area").css('display','block');
        }
        if (!detailed) {
            error += '必须添加公司详细地址,   ';
            $("#err_detailed").html('必须添加公司详细地址');
            $("#err_detailed").css('display','block');
        }
        if (!comp_content) {
            error += '必须添加公司详情,   ';
            $("#err_comp_content").html('必须添加公司详情');
            $("#err_comp_content").css('display','block');
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


    var preEle = document.getElementById("pre");
    var cityEle = document.getElementById("city");
    var areaEle = document.getElementById("area");
    var pp = "<?php echo ($enprArr['province']); ?>";
    var cc = "<?php echo ($enprArr['city']); ?>";
    var aa = "<?php echo ($enprArr['district']); ?>";
    function chg(obj) {
        var ss=obj.value; 
        //先清空市
        cityEle.options.length = 1;
        areaEle.options.length = 1;
        //alert(ss);
        cityajax(ss,1);
      
    }
    function chg2(obj) {
        var val = obj.value;
        //先清空区
        areaEle.options.length = 1;
        cityajax(val,2);
    }
    
    $(document).ready(function(){
        // 一开始加载地址    
        if(pp!=""){
            cityEle.options.length = 0;  
            areaEle.options.length = 0; 
            cityajax(pp,1);       
            cityajax(cc,2);
        }
        
    });
    function cityajax(val,type) {
        //alert(cc+'lllll'+aa);
        //alert(val+'lllll'+type);
        $.ajax({
            type: 'POST',
            url: "<?php echo U('Company/ajaxCityList');?>",
            data:"lid="+val,
            success: function(aedata){ 
                var jnobj=JSON.parse(aedata);
                //console.log(jnobj);
                //先清空区
                for (var i = 0; i < jnobj.length; i++) {
                  //声明option.<option value="pres[i]">Pres[i]</option>
                  //var op = new Option(jnobj[i].name, jnobj[i].id,true);
                  
                  //添加
                    if (type==1) {
                        //console.log(jnobj[i].id+'aaa'+cc);
                        if (cc==jnobj[i].id) {
                            var op = new Option(jnobj[i].name, jnobj[i].id,true);
                        }else{
                            var op = new Option(jnobj[i].name, jnobj[i].id);
                        }
                        cityEle.append(op);
                    }
                    if (type==2) {
                        if (aa==jnobj[i].id) {
                            var op = new Option(jnobj[i].name, jnobj[i].id,true);
                        }else{
                            var op = new Option(jnobj[i].name, jnobj[i].id);
                        }
                        areaEle.options.add(op);
                    }
                }
            },
        });
    };

</script>
</body>
</html>