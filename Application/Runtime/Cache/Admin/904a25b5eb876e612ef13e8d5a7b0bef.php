<?php if (!defined('THINK_PATH')) exit();?><form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <td style="width: 1px;" class="text-center">
                   <!--  
                        <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);">
                     -->
                </td>
                <td class="text-center">
                    <a href="javascript:sort('goods_id');">ID</a>
                </td>
                <td class="text-center">
                    <a href="javascript:sort('goods_name');" >商品名称</a>
                </td>
                <td class="text-center">
                    <a>关键词</a>
                </td>
                <td class="text-center">
                    <a href="javascript:sort('goods_sn');">货号</a>
                </td>
                <td class="text-center">
                    <a >商品图</a>
                </td>
                <td class="text-center">
                    <a href="javascript:sort('shop_price');">市场单价</a>
                </td>
                <td class="text-center">
                    <a href="javascript:void(0);">库存</a>
                </td>
                <td class="text-center">
                    <a href="javascript:sort('is_recommend');">是否推荐</a>
                </td>
               

                <td class="text-center">操作</td>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($goodsList)): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                   <td>
                         <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);">

                   </td>
                    <td class="text-center"><?php echo ($list["goods_id"]); ?></td>
                    <td class="text-center" ><div style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:200px;margin: auto"><?php echo ($list["goods_name"]); ?></div></td>
                    <td class="text-center">
                        <?php echo ($list["keywords"]); ?>
                    </td>
                    <td class="text-center"><div style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis;width:200px;margin: auto"><?php echo ($list["goods_sn"]); ?></div></td>
                    <td class="text-center">
                        
                    <img src="<?php echo json_decode($list['original_img'])[0]; ?>" width=50px;height="50px">
                    </td>
                    <td class="text-center"><?php echo ($list["shop_price"]); ?></td>
                    <td class="text-center">
                       <?php echo ($list["store_count"]); ?> 
                    </td>
                    
                    <td class="text-center">
                        <?php if($list['is_recommend'] == 0): ?><label style="color:green">推荐</label>
                            <?php else: ?>
                            <label style="color:red">不推荐</label><?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="<?php echo U('Goods/detail',array('id'=>$list['goods_id']));?>" class="btn btn-primary" title="编辑"><i class="fa fa-pencil"></i>查看详情</a>
                        <a href="javascript:void(0);" onclick="del_goods('<?php echo ($list[goods_id]); ?>')" class="btn btn-danger" title="删除"><i class="fa fa-trash-o"></i>删除</a>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>
</form>
<div class="row">
    <div class="col-sm-3 text-left"></div>
    <div class="col-sm-9 text-right"><?php echo ($page); ?></div>
</div>
<script>
    // 点击分页触发的事件
    $(".pagination  a").click(function(){
        cur_page = $(this).data('p');
        ajax_get_table('search-form2',cur_page);
    });
</script>