<?php if (!defined('THINK_PATH')) exit();?><div style="min-height:60vh;">
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


<div class="row">
    <div class="col-sm-3 text-left"></div>
    <div class="col-sm-9 text-right"><?php echo ($page); ?></div>
</div>
<script>
    // 点击分页触发的事件
    $(".pagination  a").click(function(){
        var dataId = $(".nav-tabs>.active").attr("data-id");
        cur_page = $(this).data('p');
        ajax_get_table(dataId,cur_page);
    });
    $(document).ready(function(){
      //定义商品图片的高
        var width = $('.goodimg').css('width');
        var heig = parseInt(width);//var heig = parseInt(width)*0.75 宽高比4:3
        heig = heig+"px";
        $('.goodimg').css('height',heig);
        //alert(width);
    })
</script>