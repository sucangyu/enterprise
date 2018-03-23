<?php if (!defined('THINK_PATH')) exit();?><html>
 <head></head>
 <body>
  <!DOCTYPE html>
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

 
  <link href="/enterprise/Public/plugins/daterangepicker2/daterangepicker-bs3.css" rel="stylesheet" type="text/css" /> 
  <script src="/enterprise/Public/plugins/daterangepicker2/moment.min.js" type="text/javascript"></script> 
  <script src="/enterprise/Public/plugins/daterangepicker2/daterangepicker.js" type="text/javascript"></script> 
  <style type="text/css">
  .panel-title{height: 33px;}
  .fa-list{line-height: 33px}
  .title{line-height: 33px}
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
          <i class="fa fa-list"></i> <span class="title">信息列表</span>
          <button type="button" onclick="location.href='<?php echo U('Newslist/addNew');?>'" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>添加新闻</button>
       </h3> 
      </div> 
      <div class="panel-body"> 
       <div class="navbar navbar-default"> 
        <form action="<?php echo U('Newslist/ajaxindex');?>" id="search-form2" class="navbar-form form-inline" method="post" >
        <input type="hidden" name="uid" value="<?php echo ($uid); ?>">

         <div class="form-group"> 
          <label class="control-label" for="input-new-title">信息标题</label> 
          <div class="input-group"> 
           <input type="text" name="title" placeholder="信息标题" id="input-new-title" class="input-sm" style="width:100px;" />
          </div> 
         </div> 
         <div class="form-group"> 
          <label class="control-label" for="add_time">创建日期</label> 
          <div class="input-group"> 
           <input type="text" name="time" value="<?php echo ($time); ?>" placeholder="创建日期" id="add_time" class="input-sm" /> 
           <span style="color: #ccc;font-size: 12px;">如创建日期是1月1日那选择的起始日期就是1月1日解释日期是1月2日</span>
          </div> 
         </div>
         <input type="hidden" name="order_by" value="id" /> 
         <input type="hidden" name="sort" value="desc" />
         <a href="javascript:void(0)" onclick="ajax_get_table('search-form2',1)" id="button-filter search-order" class="btn btn-primary"><i class="fa fa-search">查找</i></a>
         <!-- <button type="submit" class="btn btn-default pull-right"><i class="fa fa-file-excel-o"></i>&nbsp;导出excel</button> -->
        </form>
       </div> 
       <div id="ajax_return"> 
       </div> 
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

         $('#add_time').daterangepicker({
            format:"YYYY/MM/DD",
            singleDatePicker: false,
            showDropdowns: true,
            minDate:'2017/08/01',
            maxDate:'2030/01/01',
            startDate:'2017/08/01',
            locale : {
                applyLabel : '确定',
                cancelLabel : '取消',
                fromLabel : '起始时间',
                toLabel : '结束时间',
                customRangeLabel : '自定义',
                daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
                monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月' ],
                firstDay : 1
            }
        });
    });

    // ajax 抓取页面
    function ajax_get_table(tab,page){
        cur_page = page; //当前页面 保存为全局变量
        var url = "<?php echo U('Newslist/ajaxindex');?>";
        $.ajax({
            type : "POST",
            url:url+"?p="+page,//+tab,
            data : $('#'+tab).serialize(),// 你的formid
            success: function(data){
                $("#ajax_return").html('');
                $("#ajax_return").append(data);
            }
        });
    }

    // 点击排序
    function sort(field)
    {
        $("input[name='order_by']").val(field);
        var v = $("input[name='sort']").val() == 'desc' ? 'asc' : 'desc';
        $("input[name='sort']").val(v);
        ajax_get_table('search-form2',cur_page);
    }
</script>  
 </body>
</html>