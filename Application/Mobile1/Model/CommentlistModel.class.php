<?php
/**
 * Created by PhpStorm.
 * User: Huyh
 * Date: 2017/10/27 0027
 * Time: 下午 1:59
 */
namespace Mobile\Model;
use Think\Model;
use Think\Model\RelationModel;

class CommentlistModel extends RelationModel
{
    protected $_link = array(
        'Member'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'member',
            'foreign_key'=>'mid',
            'mapping_fields'=>'nickname,id,username,userimage'
        ),
        'Supports'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'supports',
            'foreign_key'=>'support_id',
            'mapping_fields'=>'tmoney,paystas',
        ),
    );
}
