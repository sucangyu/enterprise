<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($store_name); ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="/enterprise/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/enterprise/Public/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <!--<link href="/enterprise/Public/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />-->
    <style>#imgVerify{width: 120px;margin: 0 auto; text-align: center;display: block;}	</style>
    <script>
        function detectBrowser()
        {
            var browser = navigator.appName
            if(navigator.userAgent.indexOf("MSIE")>0){
                var b_version = navigator.appVersion
                var version = b_version.split(";");
                var trim_Version = version[1].replace(/[ ]/g,"");
                if ((browser=="Netscape"||browser=="Microsoft Internet Explorer"))
                {
                    if(trim_Version == 'MSIE8.0' || trim_Version == 'MSIE7.0' || trim_Version == 'MSIE6.0'){
                        alert('请使用IE9.0版本以上进行访问');
                        return;
                    }
                }
            }
        }
        detectBrowser();
    </script>
</head>
<body class="login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b><?php echo ($store_name); ?></b></a>
    </div>
    <form method='post' target="_parent" name="login" id="login-form" action="<?php echo U('Index/login');?>" onsubmit="return checkLogin();">
        <div class="login-box-body">
            <p class="login-box-msg">管理后台</p>
            <div class="form-group has-feedback">
                <input type="text" name="username" id="username" class="form-control" placeholder="账号" />
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" id="password" placeholder="密码" />
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <opinioncontrol realtime="true" opinion_name="vertify_code" default="true">
                    <div class="row" style="padding-right: 65px;">
                        <div class="col-xs-8">
                            <input style="width: 135px" type="text" name="verify" class="form-control" placeholder="验证码"/>
                        </div>
                        <div class="col-xs-4">
                            <img id="imgVerify" style="cursor:pointer;" src="<?php echo U('Index/verify');?>" onclick="fleshVerify()"/>
                        </div>
                    </div>
                </opinioncontrol>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary btn-block btn-flat" value="立即登陆">
            </div>
        </div>
    </form>
    <div class="margin text-center">
        <div class="copyright">
            2016-<?php echo date('Y');?> &copy; <a href="<?php echo ($site_url); ?>"><?php echo ($store_name); ?></a>
            <br/>
            <a href="<?php echo ($site_url); ?>"><?php echo ($store_name); ?></a>出品
        </div>
    </div>
</div><!-- /.login-box -->
<!-- jQuery 2.1.4 -->
<script src="/enterprise/Public/jquery-2.1.4/jquery.min.js" type="text/javascript"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="/enterprise/Public/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- iCheck -->
<script src="/enterprise/Public/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
<script src="/enterprise/Public/layer/layer.js"></script><!-- 弹窗js 参考文档 http://layer.layui.com/-->
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });

    function fleshVerify(){
        //重载验证码
        $('#imgVerify').attr('src',"<?php echo U('Index/verify?td=Math.random()');?>");
    }

    function checkLogin(){
        var username = $('#username').val();
        var password = $('#password').val();
        var verify = $('input[name="verify"]').val();
        if( username == '' || password ==''){
            layer.alert('用户名或密码不能为空', {icon: 2}); //alert('用户名或密码不能为空');
            return false;
        }else if(verify ==''){
            layer.alert('验证码不能为空', {icon: 2});
            return false;
        }else if(verify.length !=4){
            layer.alert('验证码错误', {icon: 2});
            fleshVerify();
            return false;
        }else
        {
            return true;
        }
    }

    jQuery.fn.center = function () {
        this.css("position", "absolute");
        this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) +
                        $(window).scrollTop()) - 30 + "px");
        this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) +
                        $(window).scrollLeft()) + "px");
        return this;
    }
</script>
</body>
</html>