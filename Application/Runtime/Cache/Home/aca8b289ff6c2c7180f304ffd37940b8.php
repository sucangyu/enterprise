<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title><?php echo ($enprArr["name"]); ?></title>
    <!-- Bootstrap -->
    <link href="/enterprise/Public/bootstrap/css/bootstrap.css" rel="stylesheet">
    <script src="/enterprise/Public/jquery-2.1.4/jquery.min.js"></script>
    <script src="/enterprise/Public/bootstrap/js/bootstrap.min.js"></script>
    <style type="text/css">
        .page-content{min-height: 60vh;}
    </style>
</head>
<body>
<link href="/enterprise/Public/images/logo.ico" rel="shortcut icon" type="image/x-icon" />
<style type="text/css">
    .navbar-brand img{width: 30px;height: 30px;display: inline-block;margin-right: 5px;margin-top: -5px;}
</style>
<nav class="navbar navbar-fixed-top navbar-inverse">
    <div class="container">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#"><img src="<?php echo ($enprArr["log_img"]); ?>" alt="Brand"><span><?php echo ($enprArr["name"]); ?></span></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav">
                <li class="active"><a href="<?php echo U('Index/index');?>">首页 <span class="sr-only">(current)</span></a></li>
                <li><a href="<?php echo U('Goods/index');?>">产品信息</a></li>
                <li><a href="<?php echo U('News/index');?>">最新消息</a></li>
                <li><a href="<?php echo U('Index/aboutMy');?>">关于我们</a></li>
                <li><a href="<?php echo U('Recr/index');?>">人才招聘</a></li>
              </ul>
              <ul class="nav navbar-nav navbar-right">
                <li>
                  <a href="tel:<?php echo ($enprArr["phone"]); ?>"><i class="glyphicon glyphicon-phone-alt"></i>&nbsp;
                    <?php echo ($enprArr['phone'][0]); ?>
                  </a>
                </li>
                <li><a href="#"><i class="glyphicon glyphicon-envelope"></i>&nbsp;<?php echo ($enprArr["email"]); ?></a></li>
              </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </div>
    
</nav>
<div class="container" style="margin-top: 70px;">
    <div class="page-header">
        <h1><?php echo ($newArr["title"]); ?> <span style="font-size: 12px;color: #9C9C9C;">作者:<?php echo ($newArr["uid"]); ?>&nbsp;发布时间:<?php echo (date('Y-m-d H:i',$list["time"])); ?>&nbsp;浏览量: <?php echo ($newArr["browser"]); ?></span></h1>
    </div>
    <div class="page-content">
        <?php echo ($newArr["content"]); ?>
    </div>
</div>
<div style="width: 100%;height: 50px;"></div>
<div class="idx-footer" style="font-size: 12px;line-height:20px; width: 100%;height: 50px;text-align: center;left: 0;  bottom: 0;">
	<div>备案号:xxxxxx&nbsp;/&nbsp;联系方式:<?php echo ($enprArr['phone'][0]); ?>&nbsp;/&nbsp;邮箱:<?php echo ($enprArr["email"]); ?></div>
	<div>
		©2017-2018
	    善聚信息
	    提供技术服务
	</div>
    
</div>

</body>

</html>