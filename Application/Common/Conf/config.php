<?php
return array(
    /* 模块相关配置 */
    'DEFAULT_MODULE' => 'Home',
    'MODULE_DENY_LIST' => array('Common'),
    'MODULE_ALLOW_LIST'  => array('Home','Api','Common'),
    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'c;goZsfBGAy*|pedW^80nKtY.X[Urvx=6Quh#JO{', //默认数据加>密KEY

    /* URL配置 */
    'URL_MODEL' => 2, //URL模式
    'VAR_URL_PARAMS' => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR' => '/', //PATHINFO URL分割符


    'APP_SUB_DOMAIN_DEPLOY'   =>    true, // 开启子域名配 置
    'APP_SUB_DOMAIN_RULES'    =>    array(
        'www.lianyangji.io'    => 'Home',
        'api.lianyangji.io'    => 'Api',

    ),
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'lianyangji',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'tuadd12345',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'eggcoin_',    // 数据库表前缀
    'DB_CHARSET'            =>  'utf8',      // 数据库编码
    'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
);
