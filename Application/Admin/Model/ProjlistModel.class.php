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

class ProjlistModel extends RelationModel
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
        array('title','require','项目名称必须填写！',1 ,'',3),
        array('usefor','require','资金用途或目的必须填写！',1 ,'',3),
        array('overtime','require','截止日期必须填写！',1 ,'',3),
        //array('cat_id','require','商品分类必须填写！',1 ,'',3),
//        array('cateid','0','商品分类必须填写。',1,'notequal',3),
//        array('goods_sn','','商品货号重复！',2,'unique',1),
        array('needmoney','/\d{1,10}(\.\d{1,2})?$/','目标金额式不对。',2,'regex'),
        array('getmoney','/\d{1,10}(\.\d{1,2})?$/','已筹金额格式不对。',2,'regex'),
//        array('market_price','/\d{1,10}(\.\d{1,2})?$/','市场价格式不对。',2,'regex'), // currency
//        array('weight','/\d{1,10}(\.\d{1,2})?$/','重量格式不对。',2,'regex'),
    );
    protected $_link = array(
        'Member'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'member',
            'foreign_key'=>'userid',
            'mapping_fields'=>'wxname,id,username'
        ),
        'Projcategory'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'relation_table'=>'projcategory',
            'foreign_key'=>'cateid',
            'mapping_fields'=>'title,id'
        ),
    );
}