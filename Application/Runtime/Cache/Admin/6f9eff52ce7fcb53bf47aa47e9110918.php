<?php if (!defined('THINK_PATH')) exit();?>
<form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <!-- <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"></td>
 -->
                <td class="text-center">
                    <a href="javascript:sort('id');">序号</a>
                </td>
                <td class="text-center">
                    <a>文件名</a>
                </td>
                <td class="text-center">
                    <a>备份时间</a>
                </td>
                <td class="text-center">
                    <a>文件大小</a>
                </td>
                <td class="text-center"><a>操作</a></td>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($lists)): if(is_array($lists)): $key = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($key % 2 );++$key; if($key > 1): ?><tr>
                        <!-- <td class="text-center">
                            <input type="checkbox" name="selected[]" value="6">
                            <input type="hidden" name="shipping_code[]" value="flat.flat">
                        </td> -->
                        <td class="text-center"><?php echo ($key-1); ?></td>
                        <td class="text-center"><a href="<?php echo U('System/backupMysql',array('Action'=>'download','file'=>$list));?>"><?php echo ($list); ?></a></td>
                        <td class="text-center"><?php echo (getfiletime($list,$datadir)); ?></td>
                        <td class="text-center"><?php echo (getfilesize($list,$datadir)); ?></td>
                        <td class="text-left">
                            <a data-toggle="tooltip" title="" class="btn btn-info" data-original-title="还原" onclick="return confirm('确定将数据库还原到当前备份吗？')" href="<?php echo U('System/backupMysql',array('Action'=>'RL','File'=>$list));?>" ><i class="fa fa-eye">还原</i></a>
                            <a onclick="return confirm('确定删除该备份文件吗？')" href="<?php echo U('System/backupMysql',array('Action'=>'Del','File'=>$list));?>" class="btn btn-danger" title="删除"><i class="fa fa-trash-o"></i>删除</a> 
                        </td>
                    </tr><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            </tbody>
        </table>
    </div>
</form>