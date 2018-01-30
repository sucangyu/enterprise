<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title><?php echo ($enprArr["name"]); ?></title>
    <!-- Bootstrap -->
    <link href="/Public/css/app.css" rel="stylesheet">
    <script src="/Public/js/jquery1.min.js"></script>
    <script src="/Public/js/fastclick.js"></script>
    <script src="/Public/js/app.js"></script>
</head>
<body <?php if(isset($controller)) echo ' data-controller="' . $controller . '"';?> ontouchstart>

<div class="head clearfix">
    <div class="left">
        <div class="wrapper">
            <i>用户</i>
            <span class="user_body_phone"><?php echo($user['body_phone']); ?></span>
        </div>
    </div>
    <div class="right">

                <div class="wrapperb">

            <span> 
               <a href="/account/pay">
                <img src="/Public/imgs/czz.png" width="50" height="24" /></a>  </span>
        </div>
                <div class="wrappera">
            <i>可用余额</i>
            <span class="user_body_balance"><?php echo($user['body_balance']); ?> CNY</span>
        </div>
    </div>
</div>
    


<div class="container dashboard">
    <table class="dashboard">
        <thead>
            <tr>
                <td width="50%">商品</td>
                <td>买入</td>
                <td>卖出</td>
            </tr>
        </thead>
        <tbody>
<?php foreach ($objects as $item) { ?>
            <tr data-id="<?php echo($item['id']); ?>">
                <td><div class="btys"><?php echo($item['body_name']); ?> <?php echo($item['body_name_english']); ?></div></td>
                <td class="price <?php
 if($item['body_price_previous'] > $item['body_price']) echo 'green'; else echo 'red'; ?>"><?php echo(sprintf('%.' . $item['body_price_decimal'] . 'f', $item['body_price'])); ?></td>
                <td class="price <?php
 if($item['body_price_previous'] > $item['body_price']) echo 'green'; else echo 'red'; ?>"><?php echo(sprintf('%.' . $item['body_price_decimal'] . 'f', $item['body_price'])); ?></td>
            </tr>
<?php } ?>
        </tbody>
    </table>
</div>

</body>

</html>