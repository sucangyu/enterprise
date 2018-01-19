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

    <style>#search-form > .form-group{margin-left: 10px;}</style>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 商品列表</h3>
                </div>
                <div class="panel-body">
                    <div class="navbar navbar-default">
                        <form action="" id="search-form2" class="navbar-form form-inline" method="post" onsubmit="return false">
                            <div class="form-group">
                                <label class="control-label" for="input-good-name">商品名称</label>
                                <div class="input-group">
                                    <input type="text" name="goods_name" value="" placeholder="搜索词" id="input-good-name" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="is_on_sale">是否推荐</label>
                                <select name="is_recommend" id="is_on_sale" class="form-control">
                                    <option value="">全部</option>
                                    <option value="0">推荐</option>
                                    <option value="1">不推荐</option>
                                </select>
                            </div>
                            <!-- <div class="form-group">
                                <label class="control-label" for="input-order-id">关键词</label>
                                <div class="input-group">
                                    <input type="text" name="key_word" value="" placeholder="搜索词" id="input-order-id" class="form-control">
                                </div>
                            </div> -->
                            <!--排序规则-->
                            <input type="hidden" name="orderby1" value="goods_id" />
                            <input type="hidden" name="orderby2" value="desc" />
                            <button type="submit" onclick="ajax_get_table('search-form2',1)" id="button-filter search-order" class="btn btn-primary"><i class="fa fa-search"></i> 筛选</button>
                            <button type="button" onclick="location.href='<?php echo U('Goods/goods');?>'" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>添加新商品</button>
                        </form>
                    </div>
                    <div id="ajax_return"> </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    $(document).ready(function(){
        ajax_get_table('search-form2',1);

    });
    // ajax 抓取页面
    function ajax_get_table(tab,page){
        cur_page = page; //当前页面 保存为全局变量
        var url = "<?php echo U('Goods/ajaxGoodsList');?>";
        $.ajax({
            type : "POST",
            url:url+"?p="+page,//+tab,
            data : $('#'+tab).serialize(),// 你的formid
            success: function(data){
                // alert(data);
                $("#ajax_return").html('');
                $("#ajax_return").append(data);
            },
            error:function(data){
                //alert('ajax失败');
                console.log(data);
            }
        });
    }

    // 点击排序
    function sort(field)
    {
        $("input[name='orderby1']").val(field);
        var v = $("input[name='orderby2']").val() == 'desc' ? 'asc' : 'desc';
        $("input[name='orderby2']").val(v);
        ajax_get_table('search-form2',cur_page);
    }

    // 删除操作
    function del_goods(id)
    {

        if(!confirm('确定要删除吗?'))
            return false;
        var url = "<?php echo U('Goods/delGoods');?>";
        $.ajax({
            type : "Get",
            url:url+"?id="+id,

            success: function(v){
//                alert(v);
                var v =  eval('('+v+')');
                if(v.hasOwnProperty('status') && (v.status == 1))
                    ajax_get_table('search-form2',cur_page);
                else
                    layer.msg(v.msg, {icon: 2,time: 1000}); //alert(v.msg);
            },error:function(v){
                alert('失败')
            }

        });
        return false;
    }
</script>
</body>
</html>