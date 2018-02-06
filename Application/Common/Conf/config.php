<?php
return array(
    /* 模块相关配置 */
    'DEFAULT_MODULE' => 'Home',
    'MODULE_DENY_LIST' => array('Common'),
    'MODULE_ALLOW_LIST'  => array('Home','Api'),
    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'c;goZsfBGAy*|pedW^80nKtY.X[Urvx=6Quh#JO{', //默认数据加>密KEY

    /* URL配置 */
    'URL_MODEL' => 2, //URL模式
    'VAR_URL_PARAMS' => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR' => '/', //PATHINFO URL分割符


    'APP_SUB_DOMAIN_DEPLOY'   =>    true, // 开启子域名配 置
    'APP_SUB_DOMAIN_RULES'    =>    array(
        'api.lianyangji.io'    => 'Home',

    ),

);