<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <title>链养鸡</title>
    <style>
        *{
            margin: 0;
            padding: 0;
        }
        li{
            list-style-type:none;
        }
        .nav{
            width: 100%;
            height: 50px;
            background: #333;
        }
        .nav div{
            width: 20%;
            height: 50px;
            float: left;
            text-align: center;
        }
        .nav img{
            width: 50px;
            height: 50px;
            background: #fff;
        }
        .nav ul{
            display: inline-block;
            width: 80%;
        }
        .nav li{
            display: inline-block;
            width: 25%;
            height: 50px;
            text-align: center;
            line-height: 50px;
            float: left;
            color: #fff;
        }
        .banner{
            width: 100%;
            height: 400px;
            background: url(img/banner.jpg) right;
        }
        .banner div{
            width: 100%;
            height: 80px;
            text-align: center;
            font-size: 20px;
            color: #fff;
            padding-top: 150px;
        }
        .banner p:nth-of-type(1){
            font-size: 32px;
        }
        .main{
            width: 100%;
            height: 100px;
            box-shadow: 5px 5px 5px #999;
            position: relative;
        }
        .main ul{
            width: 100%;
            height: 100px;
        }
        .main li{
            display: inline-block;
            width: 25%;
            height: 100px;
            text-align: center;
            float: left;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .main li:hover{
            color: #f7ab26;
        }
        .mains{
            width: 150px;
            height: 160px;
            position: absolute;
            right: 14%;
            top: -120px;
            background: url(img/walletbg.png) no-repeat bottom;
            text-align: center;
            display: none;
        }
        .mains img{
            width: 100px;
            height: 100px;
            margin-top: 20px;
        }
        .main:hover .mains{
            display: block;
        }

        .sum{
            width: 90%;
            margin: 5%;
            background: linear-gradient(135deg, #00D0FF, #608DDE);
            color: #fff;
            border-radius: 5px;
            padding-top: 10px;
        }
        .sum>p{
            width: 100%;
            height: 50px;
            line-height: 50px;
            text-align: center;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .sum ul{
            width: 100%;
            display: flex;
            height: 70px;
        }
        .sum li{
            width: 50%;
            text-align: center;
        }
        .sum li p:nth-of-type(1){
            font-size: 24px;
        }
        .sum li p:nth-of-type(2){
            font-size: 12px;
        }

        .average{
            width: 90%;
            margin: 5%;
            background: linear-gradient(135deg, #FF76F4, #7977FF);
            color: #fff;
            border-radius: 5px;
            padding-top: 10px;
        }
        .average>p{
            width: 100%;
            height: 50px;
            line-height: 50px;
            text-align: center;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .average ul{
            width: 100%;
            display: flex;
            height: 70px;
        }
        .average li{
            width: 33.33%;
            text-align: center;
        }
        .average li p:nth-of-type(1){
            font-size: 24px;
        }
        .average li p:nth-of-type(2){
            font-size: 12px;
        }
        .average li p i{
            font-size: 14px;
        }


        .body{
            width: 90%;
            height: 120px;
            padding: 0 5%;
            margin-top: 50px;
        }
        .body ul{
            display: inline-block;
            width: 100%;
        }
        .body a{
            text-decoration: none;
            color: #999;
            font-size: 12px;
        }
        .body li{
            display: inline-block;
            width: 25%;
            height: 120px;
            float: left;
            text-align: center;
        }
        .body li p:nth-of-type(1){
            color: #000;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .QRcode{
            width: 100%;
            height: 120px;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .QRcode img{
            width: 100px;
            height: 100px;
        }
        .line{
            width: 90%;
            height: 1px;
            background: #999;
            margin: 5%;
        }
        .right p{
            font-size: 12px;
        }
        .left p:nth-of-type(2){
            font-size: 24px;
        }
        .footer{
            text-align: center;
            width: 100%;
            margin: 20px 0;
            font-size: 12px;
            color: #999;
        }
        .footer a{
            display: inline-block;
            text-align: center;
            border-left: 1px solid #999;
            text-decoration: none;
            color: #999;
        }

        a:hover{color: skyblue;}
    </style>
</head>
<body>
<div class="nav">
    <div><img src="img/icon.png"></div>
    <ul>
        <li>首页</li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>
<div class="banner">
    <div>
        <p>链养鸡</p>
        <p>区块链实体养鸡 透明收益</p>
    </div>
</div>
<div class="main">
    <ul>
        <li>介绍</li>
        <li>微信</li>
        <li>下载</li>
        <a href="https://support.qq.com/product/22998" style="color: #000;"><li>联系我们</li></a>

    </ul>
    <div class="mains">
        <img src="img/QR.png">
    </div>
</div>

<div class="sum">
    <p>链养鸡区块链累计情况</p>
    <ul>
        <li>
            <p>1862163</p>
            <p>链养鸡挖矿产量</p>
        </li>
        <li>
            <p>641891</p>
            <p>区块高度</p>
        </li>
    </ul>
</div>
<div class="average">
    <p>链养鸡挖矿人均表现</p>
    <ul>
        <li>
            <p>21<i>小时</i></p>
            <p>人均有效上线时长</p>
        </li>
        <li>
            <p>13<i>Mbps</i></p>
            <p>人均贡献上行频宽</p>
        </li>
        <li>
            <p>1026<i>GB</i></p>
            <p>人均贡献储存</p>
        </li>
    </ul>
</div>
<div class="QRcode">
    <div class="right">
        <img src="img/QR.png">
        <p>扫码下载链养鸡APP</p>
    </div>
    <div class="left">
        <p>服务时间：10:00-19:00</p>
        <p>010-57105035</p>
    </div>

</div>
<div class="body" style="display: none;">
    <ul>
        <li>
            <p><a href="">关于我们</a></p>
            <p><a href="">网心科技</a></p>
            <p><a href="">公司简介</a></p>
            <p><a href="">发展历程</a></p>
        </li>
        <li>
            <p><a href="">媒体中心</a></p>
            <p><a href="">新闻动态</a></p>
            <p><a href="">精彩活动</a></p>
            <p><a href="">最新公告</a></p>
        </li><li>
        <p><a href="">服务支持</a></p>
        <p><a href="">常见问题</a></p>
        <p><a href="">售后服务</a></p>
        <p><a href="">联系我们</a></p>
    </li><li>
        <p><a href="">关注我们</a></p>
        <p><a href="">官方微博</a></p>
        <p><a href="">官方微信</a></p>
        <p><a href="">官方社区</a></p>
    </li>
    </ul>

</div>
<div class="line"></div>

<div class="footer">
    <span>Copyright © 2018 lianyangji.io All Rights Reserved</span>

</div>
</body>
</html>