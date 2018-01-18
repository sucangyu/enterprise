<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/27
 * Time: 12:59
 */

namespace Admin\Model;
use Think\Model;
use Think\Model\RelationModel;

class PropertylistModel extends RelationModel
{
    protected $_link=array(
        "Member" =>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table' => 'member',
            'foreign_key' => 'mid',
            'map[ing_fields' => 'username,nickname,userphone'
        ),
        "Projlist" =>array(
            'mapping_type' =>self::BELONGS_TO,
            'relation_table' => 'projlist',
            'foreign_key' => 'proj_id',
            'mapping_fields' => 'titles'
        ),

    );
}