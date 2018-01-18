<?php if (!defined('THINK_PATH')) exit();?>
<form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"></td>

                <td class="text-center">
                    <a href="javascript:sort('id');">ID</a>
                </td>
                <td class="text-center">
                    <a>友情链接名称</a>
                </td>
                <td class="text-center">
                    <a>友情链接地址</a>
                </td>
                
                <td class="text-center"><a>操作</a></td>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($linkList)): $i = 0; $__LIST__ = $linkList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                    <td class="text-center">
                        <input type="checkbox" name="selected[]" value="6">
                        <input type="hidden" name="shipping_code[]" value="flat.flat">
                    </td>
                    <td class="text-center"><?php echo ($list["id"]); ?></td>
                    <td class="text-center"><?php echo ($list["name"]); ?></td>
                    <td class="text-center"><?php echo ($list["url"]); ?></td>
                    <td class="text-left">
                        <a href="<?php echo U('Company/linkdetail',array('id'=>$list['id']));?>" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="查看详情"><i class="fa fa-eye">查看详情</i></a>
                        <a href="javascript:void(0);" onclick="del(<?php echo ($list['id']); ?>)" class="btn btn-danger" title="删除"><i class="fa fa-trash-o"></i>删除</a> 
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>
</form>
<div class="row">
    <div class="col-sm-6 text-left"></div>
    <div class="col-sm-6 text-right"><?php echo ($page); ?></div>
</div>
<script>
    $(".pagination  a").click(function(){
        var page = $(this).data('p');
        ajax_get_table('search-form2',page);
    });

    //删除操作
    function del(id,order_status){
        //alert(id);
        var url = "<?php echo U('Company/linkdel');?>";
    
        if(!confirm('确定要删除吗?'))
            return false;
        $.ajax({
            type : "GET",
            url:url,//+tab,
            data : {id:id},
            success: function(v){
                 var v =  eval('('+v+')');
                if(v.hasOwnProperty('status') && (v.status == 1))
                    location.href='<?php echo U('Company/linklist');?>';
                else
                layer.msg(v.msg, {icon: 2,time: 1000}); 
            }
        });
        return false;
    
    }
</script>