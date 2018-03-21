<?php
/**
 * Created by PhpStorm.
 * User: cjf
 * Date: 16/8/19
 * Time: 下午2:36
 */
return array(
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js'
    ),

    /*鸡状态*/
    'CHICKEN_STATE' => array(
        1=>'待认养',
        2=>'释放',
        3=>'锁定',
        4=>'待绑定',
        5=>'已认养'
    ),

    /*新闻文章来源*/
    'NEWS_COME_FROM' => array(
        1 => '微信公众号',
        2 => '微博',
        3 => '腾讯新闻',
        4 => '网易新闻',
        5 => '今日头条',
        6 => '其他',
    ),
);