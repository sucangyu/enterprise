<?php
return array(
	//'配置项'=>'配置值'
    //RBAC权限配置设置
    'RBAC_SUPERADMIN' => 'admin',//超级管理员名称,对应用户表中的某一个用户名-username
    'ADMIN_AUTH_KEY' => 'supadmin',//超级管理员识别码
    'USER_AUTH_ON' => true,  //是否需要认证
    'USER_AUTH_TYPE' => 1, //认证类型1-登录之后,2-实时认证
    'USER_AUTH_KEY' => 'authid',//认证识别号,自己可以随便定义
//    'REQUIRE_AUTH_MODULE' => 'Index',  //需要认证模块
    'NOT_AUTH_MODULE' => 'Index,Main,Home,Mobile', //无需认证模块，与上重复，可只取其一
//    'NOT_AUTH_ACTION'=>'Index,Main', //无需认证方法
//    'USER_AUTH_GATEWAY' => '/Index/index',//认证网关,此处可以不填写
//    'RBAC_DB_DSN' => '数据库类型://用户名:密码@端口号/数据库名', //数据库连接DSN
    'RBAC_ROLE_TABLE' =>  'role',//角色表名称
    'RBAC_USER_TABLE' => 'role_user',//用户表名称
    'RBAC_ACCESS_TABLE' => 'access', //权限表名称
    'RBAC_NODE_TABLE' => 'node', //节点表名称


    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => 'Public:dispatch_jump',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Public:dispatch_jump',
);