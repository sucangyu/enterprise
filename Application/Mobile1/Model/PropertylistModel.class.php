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

class PropertylistModel extends RelationModel
{
    protected $_link = array(
        'Member'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'propertylist',
            'foreign_key'=>'givemid',
            'mapping_fields'=>'username,nickename'
        ),
        'Projlist'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'projlist',
            'foreign_key'=>'proj_id',
            'mapping_fields'=>'titles,propertyname,propertypic,stats'
        ),
    );
}
