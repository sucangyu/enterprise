<?php if (!defined('THINK_PATH')) exit();?>
<form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"></td>
                <td class="text-center">
                    <a>部门名称</a>
                </td>
                <td class="text-center">
                    <a>部门负责人</a>
                </td>
                <td class="text-center">
                    <a>邮箱</a>
                </td>
                <td class="text-center">
                    <a>部门图</a>
                </td>
                <td class="text-center"><a>操作</a></td>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($deparList)): $i = 0; $__LIST__ = $deparList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                    <td class="text-center">
                        <input type="checkbox" name="selected[]" value="<?php echo ($list["id"]); ?>">
                        <input type="hidden" name="shipping_code[]" value="flat.flat">
                    </td>
                    <td class="text-center" id="title<?php echo ($list["id"]); ?>"><?php echo ($list["title"]); ?></td>
                    <td class="text-center" id="email<?php echo ($list["id"]); ?>"><?php echo ($list["email"]); ?></td>
                    <td class="text-center">
                        <div style="width: 100px;word-wrap:break-word;margin:0 auto;height: 90px;overflow:hidden"><?php echo ($list["head"]); ?>
                        </div>
                    </td>
                    <td class="text-center">
                        <?php if(!empty($list['dep_img'])): ?><img src="<?php echo ($list['dep_img']); ?>" style="width: 120px;height: 90px;"><?php endif; ?>
                    </td>
                    
                    <td class="text-left">
                        <a href="<?php echo U('Department/detail',array('id'=>$list['id']));?>" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="查看详情"><i class="fa fa-eye">查看详情</i></a>
                        <a href="javascript:void(0);" onclick="del(<?php echo ($list['id']); ?>)" class="btn btn-danger" title="删除"><i class="fa fa-trash-o"></i>删除</a> 
                        <a href="javascript:void(0);" onclick="send(<?php echo ($list['id']); ?>)" class="btn btn-info" title="发送邮件"><i class="glyphicon glyphicon-envelope"></i>发送邮件</a> 
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
        var url = "<?php echo U('Department/depardel');?>";
    
        if(!confirm('确定要删除吗?'))
            return false;
        $.ajax({
            type : "GET",
            url:url,//+tab,
            data : {id:id},
            success: function(v){
                 var v =  eval('('+v+')');
                if(v.hasOwnProperty('status') && (v.status == 1))
                    location.href='<?php echo U('Department/index');?>';
                else
                layer.msg(v.msg, {icon: 2,time: 1000}); 
            }
        });
        return false;
    
    }


    //发邮件弹出层
    function send(id){
        $("#nullEmail").html('');
        var email = $("#email"+id).html();
        var title = '';
        if (!email) {
            title = $("#title"+id).html();
        }
        if (!id) {
            var id = '';
            title = '';
            email = '';
            $("input:checkbox[name='selected[]']:checked").each(function() { // 遍历name=test的多选框
                id = $(this).val();  // 每一个被选中项的值
                var emailHtml = $("#email"+id).html();
                var titleHtml = $("#title"+id).html();
                if (emailHtml) {
                    email += emailHtml + ",";
                }else{
                    title += titleHtml + ",";
                }
            });
        }
        $("#exampleInputEmail1").val(email);
        if (title) {
            $("#nullEmail").html(title+'没有填写邮件请手动添加以英文逗号分隔');
            $("#nullEmail").css('display','block');        
        }
        if (id) {
            layer.open({
                type: 1,
                title: '邮件发送',
                shadeClose: true,
                offset: '30%',
                shade: 0.8,
                area: ['60%', '65%'],
                content: $("#email") //iframe的url
            });
        }else{
            layer.alert('请选择收件人');
        }
        
    }
</script>