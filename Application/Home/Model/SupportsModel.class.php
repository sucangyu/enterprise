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

class SupportsModel extends RelationModel
{
    protected $_link = array(
        'Projlist'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'projlist',
            'foreign_key'=>'proj_id',
            'mapping_fields'=>'titles,projimages'
        ),
    );
}
