<?php $stop_url = site_url('shipping/deliver_management/stop_add_order'); ?>
<?php $unconfirmed_count_url = site_url('shipping/deliver_management/fetch_unconfirmed_count'); ?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="/js/prototype.js"></script>
    <script type="text/javascript" src="/js/epacket.js"></script>
    <script type="text/javascript">
        document.observe('dom:loaded', function () {
            update_unconfirmed_count('<?=$unconfirmed_count_url?>');
        })
    </script>
</head>
<body>
    <br><br><br><br>
    <center>
        <font color=red>后台正在获取E邮宝track number</font><br/>
        <input type="button" value="点击停止获取E邮宝track number" onclick="stop_get_track_number('<?=$stop_url?>');">
        待获取的订单数:<font color=red><span id='unconfirmed_count'><? echo $unconfirmed_count ?></span></font>
        <span id="loading"><img src="/static/img/loading.gif" title="Loading the list"/></span>
    </center>
</body>
