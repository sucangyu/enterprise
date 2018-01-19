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
        .demo{padding: 2em 0;}
        .box{text-align: center;overflow: hidden;position: relative;}
        .box:before{content: "";width: 0;height: 100%;background: #000;padding: 14px 18px;position: absolute;top: 0;left: 50%;opacity: 0;transition: all 500ms cubic-bezier(0.47, 0, 0.745, 0.715) 0s;}
        .box:hover:before{width: 100%;left: 0;opacity: 0.5;}
        .box img{width: 100%;height: 25vh;}
        .box .box-content{width: 100%;padding: 14px 18px;color: #fff;position: absolute;top: 1%;left: 0;}
        .box .post{ margin-top: 1vh;}
        .box .title{font-size: 25px;font-weight: 600;line-height: 30px;text-transform: uppercase;margin: 0;opacity: 0;transition: all 0.5s ease 0s;}
        .box .post{font-size: 15px;text-transform: capitalize;opacity: 0;transition: all 0.5s ease 0s;}
        .box:hover .title,
        .box:hover .post{opacity: 1;transition-delay: 0.7s;}
        .box .icon{padding: 0;margin: 0;list-style: none;margin-top: 15px;}
        .box .icon li{display: inline-block;}
        /*.box .icon li a{display: block;width: 40px;height: 40px;line-height: 40px;border-radius: 50%;background: #f74e55;font-size: 20px;font-weight: 700;color: #fff;margin-right: 5px;opacity: 0;transform: translateY(50px);*/transition: all 0.5s ease 0s;}
        .box:hover .icon li a{opacity: 1;transform: translateY(0px);transition-delay: 0.5s;}
        .box:hover .icon li:last-child a{transition-delay: 0.8s;}
        @media only screen and (max-width:990px){.box{ margin-bottom: 30px; }}
        .row{min-height: 60vh;}
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
    <div class="row">
        <div class="col-lg-9 col-sm-9 col-xs-12">
            <div class="page-header">
                <h3><?php echo ($recruiArr["title"]); ?> <small><?php echo ($recruiArr["treatment"]); ?></small></h3>
            </div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <span style="margin-left: 10px;">招聘职位: <?php echo ($recruiArr["position"]); ?></span>
                <span style="margin-left: 10px;">工资待遇: <?php echo ($recruiArr["salary"]); ?></span>
              </div>
              <div class="panel-body">
                <div class="alert alert-info" role="alert" style="overflow: auto;">
                    <div class="col-sm-3 col-xs-12">招聘人数: <?php echo ($recruiArr["nums"]); ?>(人)</div>
                    <div class="col-sm-3 col-xs-12">所需学历: <?php echo ($recruiArr["schooling"]); ?></div>
                    <div class="col-sm-3 col-xs-12">所需经验: <?php echo ($recruiArr["experience"]); ?></div>
                    <div class="col-sm-3 col-xs-12">聘任部门: <?php echo ($recruiArr['Department']['title']); ?></div>
                </div>
                <h4  style="margin-top: 10px;">职位描述</h4>
                <div class="panel panel-default">
                  <div class="panel-body">
                    <?php echo ($recruiArr["content"]); ?>
                  </div>
                </div>
                <p>信息发布者:<?php echo ($recruiArr["publisher"]); ?></p>
              </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-3 col-xs-12">
            <div class="page-header">
              <h4>公司下属部门</h4>
              <a href="<?php echo U('Index/aboutMy');?>">更多</a>
            </div>
            <div class="rew">
                <?php if(is_array($deparList)): $i = 0; $__LIST__ = $deparList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div style="margin-top: 10px;">
                        <div class="box">
                            <img src="<?php echo ($vo["dep_img"]); ?>" class="dep_img img-thumbnail">
                            <div class="box-content">
                                <h3 class="title"><?php echo ($vo["title"]); ?></h3>
                                <span class="post"><?php echo ($vo["head"]); ?></span>
                                <p class="post text" style="font-size: 0.1vw;">
                                    <?php echo ($vo["content"]); ?>
                                </p>
                                <!-- <ul class="icon">
                                    <li><a href="#"><i class="fa fa-search"></i></a></li>
                                    <li><a href="#"><i class="fa fa-link"></i></a></li>
                                </ul> -->
                            </div>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
        
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