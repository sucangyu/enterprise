<?php
/**
 * Created by PhpStorm.
 * User: huyh
 * Date: 15-7-2
 * use:树形无限极分类
 * Time: 下午1:46
 */

namespace Org\Util;
class Tree
{
    static public $treeList = array();//存放无限极分类结果

    public function create($data,$pid=0)
    {
        foreach($data as $key=>$val)
        {
            if($val['pid'] == $pid)
            {
                self::$treeList[]=$val;
                unset($data['$key']);
                self::create($data,$val['id']);
            }
        }
        return self::$treeList;
    }
}
