<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Use:
 * Date: 2016/6/13
 * Time: 17:19
 */
namespace Admin\Model;
use Think\Model;
use Think\Model\RelationModel;

class NodeModel extends RelationModel
{
    protected $_link = array(
        'Node'=>array(
            'mapping_type'=> self::HAS_MANY,
            'mapping_name' => 'node',
            'mapping_order' => 'sort',
            'parent_key'=>'pid',
        ),
    );
}