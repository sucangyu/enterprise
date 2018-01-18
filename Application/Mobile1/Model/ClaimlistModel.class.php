<?php
/**
 * Created by PhpStorm.
 * User: Huyh
 * Date: 2017/10/31 0027
 * Time: 上午 9:57
 */
namespace Mobile\Model;
use Think\Model;
use Think\Model\RelationModel;

class ClaimlistModel extends RelationModel
{
    protected $_link = array(
        'Propertylist'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'propertylist',
            'foreign_key'=>'property_id',
            'mapping_fields'=>'treenum'
        ),
        'Projchild'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'projchild',
            'foreign_key'=>'pc_id',
            'mapping_fields'=>'pc_title,pc_pic,minpropertynum,stats'
        ),
        'Projlist'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'projlist',
            'foreign_key'=>'proj_id',
            'mapping_fields'=>'stats'
        ),
    );
}
