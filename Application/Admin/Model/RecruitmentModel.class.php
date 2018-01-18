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