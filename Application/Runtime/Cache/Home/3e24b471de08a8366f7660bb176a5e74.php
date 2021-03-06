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
        .carousel-inner .item a img{width: 100%;height: 32vh;}
        .jumbotron {padding-top: 30px;padding-bottom: 30px;margin-top: 30px;}
        .jumbotron p{word-wrap:break-word;color: #919191;font-size: 16px;}
        .page-header{margin: 10px 0 20px;}
        .page-header h4{display: inline-block;}
        .page-header a{float: right;line-height: 49px;}
        .shell{ border:1px solid #eee; width:100%; padding:5px 2px; margin-left: 0px;border-radius:5px;overflow: auto;}
        .new-img{width: 90%;float: left;height: 16vh;margin-bottom: 2vh;margin-left: 5%}
        #one{ height:16vh; overflow:hidden; float: left;width: 90%;margin-left: 0px;} 
        #one a{text-decoration:none;font-family:Arial;font-size:16px;color:#333;height: 22px;} 
        #one li{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;margin-bottom: 15px;margin-left: -10px;}
        #one li:hover{ background-color:#eee;}
        .caption h4{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
        .goodimg{width: 100%;}
        .media-object{width: 100px;height: 100px;}
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
  <!-- 轮播组件 -->
    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
      <!-- Wrapper for slides -->
      <div class="carousel-inner" role="listbox">
        <?php if(is_array($shufflingArr)): $k = 0; $__LIST__ = $shufflingArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="item">
              <a href="<?php echo ($vo["a_href"]); ?>"><img src="<?php echo ($vo["img_src"]); ?>" alt="<?php echo ($vo["alt"]); ?>" title="<?php echo ($vo["alt"]); ?>"></a>
              
              <div class="carousel-caption">
              </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
      </div>
      <!-- Controls -->
      <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div><!-- 轮播组件end -->  


    <!-- 动态新闻和产品 -->
    <div class="row">
        <!--新闻-->
        <div class="col-lg-4 col-sm-4 col-xs-12">
            <div class="page-header">
                <h4>公司信息</h4>
            </div>
            <ul class="list-group">
                <li class="list-group-item"><i class="glyphicon glyphicon-map-marker">&nbsp;</i>
                <?php echo ($ress["province"]); ?>&nbsp;<?php echo ($ress["city"]); ?>&nbsp;<?php echo ($ress["district"]); ?>&nbsp;<?php echo ($enprArr["address"]); ?>
                </li>
                <li class="list-group-item"><i class="glyphicon glyphicon-envelope">&nbsp;</i>
                <?php echo ($enprArr["email"]); ?>
                </li>
                <li class="list-group-item"><i class="glyphicon glyphicon-print">&nbsp;</i><?php echo ($enprArr["fax"]); ?>
                </li>
                <li class="list-group-item"><i class="glyphicon glyphicon-comment">&nbsp;</i><?php echo ($enprArr["qq"]); ?>
                </li>
                <?php if(is_array($enprArr["phone"])): $p = 0; $__LIST__ = $enprArr["phone"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo5): $mod = ($p % 2 );++$p;?><li class="list-group-item"><i class="glyphicon glyphicon-earphone">&nbsp;</i> 
                      <a href="tel:<?php echo ($vo5); ?>" style="color: #333"><?php echo ($p); ?>: <?php echo ($vo5); ?></a>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
            <div class="page-header">
                <h4>最新产品 <small>NEW GOOD</small></h4>
                <a href="<?php echo U('Goods/index');?>">更多</a>
            </div>
            <?php if(is_array($goodlist)): $i = 0; $__LIST__ = $goodlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;?><div class="thumbnail">
                    <a href="<?php echo U('Goods/detail',array('id'=>$vo2['goods_id']));?>"><img class="goodimg" src="<?php echo json_decode($vo2['original_img'])[0]; ?>" alt="..."/></a>
                    <div class="caption">
                        <h4><a href="<?php echo U('Goods/detail',array('id'=>$vo2['goods_id']));?>"><?php echo ($vo2["goods_name"]); ?></a></h4>
                        <p>
                            <?php if($vo2["market_price"] != 0.00 ): ?><s><?php echo ($vo2["market_price"]); ?></s>&nbsp;<?php endif; ?>
                            <strong style="color: red;"><?php echo ($vo2["shop_price"]); ?></strong>
                        </p>
                        <!-- <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p> -->
                    </div>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
            
            <script> 
                $(document).ready(function(){
                  //定义商品图片的高
                    var width = $('.goodimg').css('width');
                    var heig = parseInt(width);//var heig = parseInt(width)*0.75 宽高比4:3
                    heig = heig+"px";
                    $('.goodimg').css('height',heig);
                    //alert(width);
                    //为第一张轮播图添加class名
                    $(".item:first").addClass("active");
                    //加载第一页
                    ajax_get_table('',1);
                })

                // ajax 抓取页面
                function ajax_get_table(tab,page){
                    cur_page = page; //当前页面 保存为全局变量
                    var url = "<?php echo U('News/ajaxindex');?>";
                    $.ajax({
                        type : "POST",
                        url:url+"?p="+page,//+tab,
                        data : {cat_id:tab},// 商品分类
                        success: function(data){
                            $("#ajax_return").html('');
                            $("#ajax_return").append(data);
                        }
                    });
                }

            </script> 
        </div>
        <!--产品-->
        <div class="col-lg-8 col-sm-8 col-xs-12">
            <div class="page-header">
              <h4>最新资讯 <small>NEWS</small></h4>
            </div>
            <div id="ajax_return">

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