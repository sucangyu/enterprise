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

class OrdergoodsModel extends RelationModel
{
    protected $_link = array(
        'Goods'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'Goods',
            'foreign_key'=>'goods_id',
            'mapping_fields'=>'id,titles,titlepic'
        ),
    );
}
