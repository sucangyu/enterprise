<?php
return array(
	//'配置项'=>'配置值'
    //数据库配置信息
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'testqiye', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root', // 密码
//    'DB_PWD'    => 'root@)!^root', // 密码
    'DB_PORT'   => 3306, // 端口
//    'DB_PREFIX' => '', // 数据库表前缀
    'DB_CHARSET'=> 'utf8', // 字符集
//    'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
//    'SHOW_PAGE_Trace'=>true,
//     'SESSION_PRE'=>'',//session 前缀
    //即时通讯(融云即时通讯在线聊天)
    'RONG_IS_DEV'            => true,//是否是在开发中
    'RONG_DEV_APP_KEY'       => '8luwapkvu3xwl', //融云开发环境下的key    仅供测试使用
    'RONG_DEV_APP_SECRET'    => '1Aw1D7F6Td25', //融云开发环境下的SECRET  仅供测试使用
    'RONG_PRO_APP_KEY'       => '', //融云生产环境下的key
    'RONG_PRO_APP_SECRET'    => '', //融云生产环境下的SECRET
    //群发邮件
    'EMAIL_FROM_NAME'        => '系统',        // 发件人
    'EMAIL_SMTP'             => 'smtp.qq.com',  // smtp
    'EMAIL_USERNAME'         => '727817393@qq.com',        // 账号
    'EMAIL_PASSWORD'         => 'fueimhnzyyjgbfjd',        // 密码  注意: 163和QQ邮箱是授权码；不是登录的密码
    'EMAIL_SMTP_SECURE'      => 'ssl',          // 如果使用QQ邮箱；需要把此项改为  ssl
    'EMAIL_PORT'             => '465',          // 如果使用QQ邮箱；需要把此项改为  465
);