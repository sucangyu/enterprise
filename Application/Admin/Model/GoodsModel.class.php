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

class GoodsModel extends RelationModel
{
    protected $patchValidate = true; // 系统支持数据的批量验证功能，
    /**
     *
    self::EXISTS_VALIDATE 或者0 存在字段就验证（默认）
    self::MUST_VALIDATE 或者1 必须验证
    self::VALUE_VALIDATE或者2 值不为空的时候验证
     *
     *
    self::MODEL_INSERT或者1新增数据时候验证
    self::MODEL_UPDATE或者2编辑数据时候验证
    self::MODEL_BOTH或者3全部情况下验证（默认）
     */
    protected $_validate = array(
        array('title','require','商品名称必须填写！',1 ,'',3),
//        array('num','require','商品库存必须填写！',1 ,'',3),
//        array('cateid','0','商品分类必须填写。',1,'notequal',3),
//        array('goods_sn','','商品货号重复！',2,'unique',1),
        array('num','/\d{1,10}(\.\d{1,2})?$/','商品库存不对。',2,'regex'),
        array('price','/\d{1,10}(\.\d{1,2})?$/','商品会员价格不对。',2,'regex'),
        array('price_0','/\d{1,10}(\.\d{1,2})?$/','商品会员价格不对。',2,'regex'),
        array('price_1','/\d{1,10}(\.\d{1,2})?$/','商品vip价格不对。',2,'regex'),
        array('price_2','/\d{1,10}(\.\d{1,2})?$/','商品代理价格不对。',2,'regex'),
        array('askdown','/\d{1,10}(\.\d{1,2})?$/','直推数不对，可以为0。',2,'regex'),
//        array('market_price','/\d{1,10}(\.\d{1,2})?$/','市场价格式不对。',2,'regex'), // currency
//        array('weight','/\d{1,10}(\.\d{1,2})?$/','重量格式不对。',2,'regex'),
    );
    protected $_link = array(
        'Goodscategory'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'goodscategory',
            'foreign_key'=>'cateid',
            'mapping_fields'=>'title,id'
        ),
    );
}