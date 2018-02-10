<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>fail</title>
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
        .nav img{
            width: 50px;
            height: 50px;
            margin-left: 50px;
            background: #fff;
        }
        .nav ul{
            display: inline-block;
            margin-left: 20px;
        }
        .nav li{
            display: inline-block;
            width: 100px;
            height: 50px;
            text-align: center;
            line-height: 50px;
            float: left;
            color: #fff;
        }
        .box{
            padding: 30px 20px;
        }
        .over{
            width: 30px;
            height: 30px;
            background: red;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            color: #fff;
            float: left;
        }
        span{
            display: inline-block;
            width: 100px;
            height: 30px;
            line-height: 30px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="nav">
    <img src="">
    <ul>
        <li>链养鸡</li>
    </ul>
</div>
<div class="box">
    <div class="over">!</div>
    <span><?php echo ($msg); ?></span>
</div>
</body>
</html>