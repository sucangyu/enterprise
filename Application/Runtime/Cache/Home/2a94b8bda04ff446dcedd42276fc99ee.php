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
    <link href="/enterprise/Public/css/home/style.css" rel="stylesheet">
    <link href="/enterprise/Public/css/home/book.css" rel="stylesheet">
    <script src="/enterprise/Public/jquery-2.1.4/jquery.min.js"></script>
    <script src="/enterprise/Public/bootstrap/js/bootstrap.js"></script>
    <script src="/enterprise/Public/js/modernizr.custom.79639.js"></script>
    <script src="/enterprise/Public/js/jquery.swatchbook.js"></script>
    <style type="text/css">
        .carousel-inner .item a img{width: 100%;height: 32vh;}
        .jumbotron {padding-top: 30px;padding-bottom: 30px;margin-top: 30px;}
        .jumbotron p{word-wrap:break-word;color: #919191;font-size: 16px;}
        .page-header{margin: 10px 0 20px;}
        .page-header h4{display: inline-block;}
        .page-header a{float: right;line-height: 49px;}
        .shell{ border:1px solid #eee; width:100%; padding:5px 2px; margin-left: 0px;border-radius:5px;overflow: auto;}
        .new-img{width: 90%;float: left;height: 16vh;margin-bottom: 2vh;margin-left: 5%}
        #one{ height:16vh; overflow:hidden; float: left;width: 90%;margin-left: 5%;} 
        #one a{text-decoration:none;font-family:Arial;font-size:16px;color:#333;height: 22px;line-height: 22px; display: inline-block;width: 92%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;} 
        
        #one li{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;margin-bottom: 15px;margin-left: 0px;height: 24px;line-height: 24px;}
        #one li .badge{margin-top: -15px;}
        #one li:hover{ background-color:#eee;}
        .caption h4{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
        .goodimg{width: 100%;}
        .dep_img{width: 80px;height: 80px}
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
      <!-- Indicators -->
      <!-- <ol class="carousel-indicators">
        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
        <li data-target="#carousel-example-generic" data-slide-to="2"></li> 
      </ol>-->
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

    <!-- 公司简介 -->
    <div class="jumbotron">
        
        <div class="row">
            <div class="col-lg-9 col-sm-9 col-xs-12">
                <h4>公司简介</h4>
                <?php echo ($enprArr["desc"]); ?>
            </div>
            <!-- 右侧部门特效 -->
            <div class="col-lg-3 col-sm-3 col-xs-12">
                
                <?php if(($deparNum) == "6"): ?><!-- 部门数等于6 -->
                    <div class="wrap">   <!--最大的空间div-->
                        <div class="cube">   <!--正方体的box-->
                            <!--这个是大的正方体的六面-->
                            <div class="out-front"><?php echo ($deparList[0]['title']); ?></div>
                            <div class="out-back"><?php echo ($deparList[1]['title']); ?></div>
                            <div class="out-left"><?php echo ($deparList[2]['title']); ?></div>
                            <div class="out-right"><?php echo ($deparList[3]['title']); ?></div>
                            <div class="out-top"><?php echo ($deparList[4]['title']); ?></div>
                            <div class="out-bottom"><?php echo ($deparList[5]['title']); ?></div>

                            <!--这个是小的正方体的六面-->
                            <span class="in-front">             <!--前面的点数1-->
                                <img src="<?php echo ($deparList[0]['dep_img']); ?>" class="dep_img img-rounded">
                            </span>
                            <span class="in-back">              <!--后面的点数3-->
                                <img src="<?php echo ($deparList[1]['dep_img']); ?>" class="dep_img img-rounded">
                            </span>
                            <span class="in-left">              <!--左面的点数2-->
                                <img src="<?php echo ($deparList[2]['dep_img']); ?>" class="dep_img img-rounded">
                            </span>
                            <span class="in-right">             <!--右面的点数4-->
                                <img src="<?php echo ($deparList[3]['dep_img']); ?>" class="dep_img img-rounded">
                            </span>
                            <span class="in-top">               <!--上面的点数5-->
                                <img src="<?php echo ($deparList[4]['dep_img']); ?>" class="dep_img img-rounded">
                            </span>
                            <span class="in-bottom">            <!--下面的点数6-->
                                <img src="<?php echo ($deparList[5]['dep_img']); ?>" class="dep_img img-rounded">
                            </span>
                        </div>
                    </div>
                <?php else: ?>
                    <section class="main">
            
                        <div id="sb-container" class="sb-container">
                            <?php if(is_array($deparList)): $i = 0; $__LIST__ = $deparList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div>
                                    <span class="glyphicon glyphicon-inbox" style="font-size: 4vh;"></span>
                                    <h4><span><?php echo ($vo["title"]); ?></span></h4>
                                </div><?php endforeach; endif; else: echo "" ;endif; ?>
                            
                            <div>
                                <h4><span>部门</span></h4>
                                <h5><span>department</span></h5>                                         
                            </div>
                            
                            
                        </div><!-- sb-container -->
                        
                    </section><?php endif; ?>
                    
                
            </div>
        </div>
    </div>
    <!-- 动态新闻和产品 -->
    <div class="row">
        <!--新闻-->
        <div class="col-lg-4 col-sm-4 col-xs-12">
            <div class="page-header">
              <h4>最新资讯 <small>NEWS</small></h4>
              <a href="<?php echo U('News/index');?>">更多</a>
            </div>

            <div class="shell"> 
                <img src="/enterprise/Public/images/new.jpg" class="new-img" alt="最新资讯">
                <div id="one">
                <div id="test">
                    <ol>
                        <?php if(is_array($newlist)): $k = 0; $__LIST__ = $newlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($k % 2 );++$k;?><li><a href="<?php echo U('News/detail',array('id'=>$vo1['id']));?>"><?php echo ($vo1["title"]); ?></a><span class="badge"><?php echo ($vo1["browser"]); ?></span></li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ol>
                </div> 
                <div id="test2"></div>
                </div>
            </div> 
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
                })

                var one =document.getElementById("one");
                var test1=document.getElementById("test");
                var test2=document.getElementById("test2");
                    test2.innerHTML=test1.innerHTML;
                var dt=setInterval(function(){                    
                        if(test1.offsetHeight-one.scrollTop<=0){
                               one.scrollTop-=test1.offsetHeight;
                        }
                        one.scrollTop++; 
                    },30);


                $(function() {
                    var aa = <?php echo ($deparNum); ?>;
                    var bb = 0
                    if (aa<6) {
                        bb = 2;
                    }else if (aa>6&&aa<=9) {
                        bb = 3
                    }else if (aa>9&&aa<12) {
                        bb = 5
                    }
                    $( '#sb-container' ).swatchbook({center : bb});
                
                });
            </script> 
        </div>
        <!--产品-->
        <div class="col-lg-8 col-sm-8 col-xs-12">
            <div class="page-header">
              <h4>最新产品 <small>NEW GOOD</small></h4>
              <a href="<?php echo U('Goods/index');?>">更多</a>
            </div>
            <div class="row goods">
                <?php if(is_array($goodlist)): $i = 0; $__LIST__ = $goodlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;?><div class="col-sm-6 col-md-4">
                        <div class="thumbnail">
                            <a href="<?php echo U('Goods/detail',array('id'=>$vo2['goods_id']));?>"><img class="goodimg" src="<?php echo json_decode($vo2['original_img'])[0]; ?>" alt="..."/></a>
                            <div class="caption">
                                <h4><a href="<?php echo U('Goods/detail',array('id'=>$vo2['goods_id']));?>"><?php echo ($vo2["goods_name"]); ?></a></h4>
                                <p>
                                    <?php if($vo2["market_price"] != 0.00 ): ?><s><?php echo ($vo2["market_price"]); ?></s>&nbsp;<?php endif; ?>
                                    <strong style="color: red;"><?php echo ($vo2["shop_price"]); ?></strong>
                                </p>
                                <!-- <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p> -->
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