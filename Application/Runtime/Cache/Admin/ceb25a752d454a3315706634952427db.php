<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($store_name); ?>管理后台</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Bootstrap 3.3.4 -->
    <link href="/enterprise/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome 4.3.0 -->
    <link href="/enterprise/Public/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 --
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="/enterprise/Public/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="/enterprise/Public/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="/enterprise/Public/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery 2.1.4 -->
    <script src="/enterprise/Public/jquery-2.1.4/jquery.min.js"></script>
   <!-- <script src="/enterprise/Public/js/global.js"></script>
    <script src="/enterprise/Public/js/upgrade.js"></script>-->
    <script src="/enterprise/Public/layer/layer.js"></script><!--弹窗js 参考文档 http://layer.layui.com/-->
    <style type="text/css">
        #riframe{min-height:inherit !important}
    </style>
</head>
<body class="skin-green-light sidebar-mini" style="overflow-y:hidden;">
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo U('Main/index');?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b></b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><img src="/enterprise/Public/images/logo.jpg" width="40" height="30">&nbsp;&nbsp;<b><?php echo ($store_name); ?></b></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!--<li>
                        <a href="<?php echo U('/Admin/System/cleanCache');?>">
                            <i class="glyphicon glyphicon glyphicon-refresh"></i>
                            <span>清除缓存</span>
                        </a>
                    </li>-->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!--  <img src="/enterprise/Public/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
                            <i class="glyphicon glyphicon-user"></i>
                            <span class="hidden-xs">欢迎：<?php echo (session('admin_name')); ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-footer">
                                <div class="pull-left">
                                    <!--<a href="<?php echo U('Webset/index');?>" target="rightContent" class="btn btn-default btn-flat model-map">网站设置</a>-->
                                    <a href="<?php echo U('Webset/index');?>" target="rightContent" class="btn btn-default btn-flat">网站设置</a>
                                    <a href="<?php echo U('Rbac/changePWD');?>" target="rightContent" class="btn btn-default btn-flat">修改密码</a>
                                    <a href="<?php echo U('Index/login_out');?>" class="btn btn-default btn-flat">安全退出</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <li><a href="#" data-toggle="control-sidebar"><i class="fa fa-street-view"></i>换肤</a></li>
                </ul>
            </div>
        </nav>
    </header>

<!--
    <script>

        // 没有点击收货确定的按钮让他自己收货确定
        var timestamp = Date.parse(new Date());
        $.ajax({
            type:'post',
            url:"<?php echo U('Admin/System/login_task');?>",
            data:{timestamp:timestamp},
            timeout : 100000000, //超时时间设置，单位毫秒
            success:function(){
                // 执行定时任务
            }
        });
    </script>-->


<aside class="main-sidebar" style="overflow-y:auto;">
  <section class="sidebar">
    <ul class="sidebar-menu">
      <?php if(is_array($node)): $i = 0; $__LIST__ = $node;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="treeview">
          <a href="javascript:void(0)">
            <i class="fa <?php echo ($vo["remark"]); ?>"></i><span><?php echo ($vo["title"]); ?></span><i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <?php if(is_array($vo['node'])): $i = 0; $__LIST__ = $vo['node'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;?><li onclick="makecss(this,<?php echo ($vv["id"]); ?>)" id="menu_<?php echo ($vv["id"]); ?>"><a href="/enterprise/index.php/Admin/<?php echo ($vo["name"]); ?>/<?php echo ($vv["name"]); ?>" target='rightContent'><i class="fa fa-circle-o"></i><?php echo ($vv["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
        </li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
  </section>
</aside>

<section class="content-wrapper right-side" id="riframe" style="margin:0px;padding:0px;margin-left:230px;">
    <iframe id='rightContent' name='rightContent' src="<?php echo U('Main/welcome');?>" width='100%' frameborder="0"></iframe>
</section>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        感谢使用<?php echo ($store_name); ?>系统<b></b>
    </div>
    <strong>Copyright &copy; 2016 <a href="<?php echo ($site_url); ?>"><?php echo ($store_name); ?></a>.</strong>保留所有权利。
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <!--
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-at"></i></a></li>
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    -->
    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane" id="control-sidebar-home-tab">
        </div><!-- /.tab-pane -->
        <!-- Stats tab content -->
        <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div><!-- /.tab-pane -->
        <!-- Settings tab content -->
        <div class="tab-pane" id="control-sidebar-settings-tab">
        </div>
    </div>
</aside>
<div class="control-sidebar-bg"></div>
</div>

<script src="/enterprise/Public/plugins/jQueryUI/jquery-ui.min.js" type="text/javascript"></script>
<script src="/enterprise/Public/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/enterprise/Public/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="/enterprise/Public/plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
<script src="/enterprise/Public/dist/js/app.js" type="text/javascript"></script>
<script src="/enterprise/Public/dist/js/demo.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $("#riframe").height($(window).height()-100);//浏览器当前窗口可视区域高度
        $("#rightContent").height($(window).height()-100);
        $('.main-sidebar').height($(window).height()-50);
    });

    var tmpmenu = 1;
    function makecss(obj,mod_id){
        $('#menu_'+tmpmenu).removeClass('active');
        $(obj).addClass('active');
        tmpmenu = mod_id;
    }

    $('.model-map').click(function(){
        var url = $(this).attr('data-url');
        layer.open({
            type: 2,
            title: '网站地图',
            shadeClose: true,
            shade: 0.8,
            area: ['70%', '60%'],
            content: url,
        });
    });

    function callUrl(url){
        layer.closeAll('iframe');
        rightContent.location.href = url;
    }
    var now_num = 0; //现在的数量
    var is_close=0;
    function ajaxOrderNotice(){
        var url = '<?php echo U("Order/ajaxOrderNotice");?>';
        if(is_close > 0)
            return;
        $.get(url,function(data){
            //有新订单且数量不跟上次相等 弹出提示
            if(data > 0 && data != now_num){
                now_num = data;
                if(document.getElementById('ordfoo').style.display == 'none'){
                    $('#orderAmount').text(data);
                    $('#ordfoo').show();
                }
            }
        })
//        setTimeout('ajaxOrderNotice()',5000);
    }
    //setTimeout('ajaxOrderNotice()',5000);
</script>
<!-- 新订单提醒-s -->
<style type="text/css">
    .fl{ float:left; margin-left:10px; margin-top:4px}
    .fr{ float:right; margin-right:10px; margin-top:3px}
    .orderfoods{ width:200px; border:1px solid #dedede; position:absolute; bottom:50px; z-index:999; right:10px; background-color:#00A65A;opacity:0.8;-webkit-opacity:0.8;filter:alpha(opacity=80);-moz-opacity:0.8;  }
    .dor_head{ border-bottom:1px solid #dedede; height:28px; color:#FFF; font-size:12px}
    .dor_head:after{ content:""; clear:both; display:block}
    .dor_foot{ margin-top:6px; color:#FFF}
    .dor_foot p{ padding:0 12px}
    .te-in{ text-indent:2em;}
    .dor_foot p span{ color:red}
    .te-al-ce{ text-align:center}
</style>
<!--<div id="ordfoo" class="orderfoods" style="">
    <div class="dor_head">
        <p class="fl">新订单通知</p>
        <p onClick="closes();" id="close" class="fr" style="cursor:pointer">x</p>
    </div>
    <div class="dor_foot">
        <p class="te-in">您有<span id="orderAmount"><?php echo ($order_amount); ?></span>个订单待处理</p>
        <p class="te-al-ce"><a href="<?php echo U('Order/index');?>" target='rightContent'><span>点击查看</span></a></p>
    </div>
</div>-->
<script type="text/javascript">
    function closes(){
        is_close = 1;
        document.getElementById('ordfoo').style.display = 'none';
    }
</script>
<!-- 新订单提醒-e -->
</body>
</html>