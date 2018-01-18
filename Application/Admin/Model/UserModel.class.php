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

class UserModel extends RelationModel
{
    protected $_link = array(
        'Role'=>array(
            'mapping_type'=>self::MANY_TO_MANY,
            'relation_table'=>'role_user',
            'foreign_key'=>'user_id',
            'relation_key'=>'role_id',
            'mapping_fields'=>'name,id'
        ),
    );
}