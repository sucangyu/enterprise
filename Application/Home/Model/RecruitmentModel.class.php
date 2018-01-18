<?php
/**
 * Created by PhpStorm.
 * User: Huyh
 * Date: 2017/10/31 0027
 * Time: 上午 9:57
 */
namespace Home\Model;
use Think\Model;
use Think\Model\RelationModel;

class RecruitmentModel extends RelationModel
{
    protected $_link = array(
        'Department'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'department',
            'foreign_key'=>'dep_id',
            'mapping_fields'=>'title,head'
        ),
    );
}
