<?php if (!defined('THINK_PATH')) exit();?><div style="min-height:60vh;">
<?php if(is_array($newlist)): $a = 0; $__LIST__ = $newlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($a % 2 );++$a;?><div class="media">
      <div class="media-left">
        <a href="<?php echo U('News/detail',array('id'=>$vo1['id']));?>">
            <?php if(empty($vo1["paperimg"])): ?><img class="media-object" src="/enterprise/Public/images/new.jpg" alt="...">
            <?php else: ?> 
            <img class="media-object" src="<?php echo ($vo1["paperimg"]); ?>" alt="..."><?php endif; ?> 
          
        </a>
      </div>
      <div class="media-body">
        <h4 class="media-heading">
            <a href="<?php echo U('News/detail',array('id'=>$vo1['id']));?>"><?php echo ($vo1["title"]); ?></a>
        </h4>

        <?php echo ($vo1["paper"]); ?>
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
</script>