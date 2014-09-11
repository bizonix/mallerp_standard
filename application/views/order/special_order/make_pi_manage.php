
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">

    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Mallerp management system</title>

        <style type='text/css' media='all'>@import url('http://192.168.1.107/static/css/main.css');</style>

        <style type='text/css' media='all'>@import url('http://192.168.1.107/static/css/nav/dropdown.css');</style>

        <style type='text/css' media='all'>@import url('http://192.168.1.107/static/css/modalbox.css');</style>

        <script type="text/javascript" src="http://192.168.1.107/static/js/lib/prototype.js"> </script>

        <script type="text/javascript" src="http://192.168.1.107/static/js/lib/scriptaculous.js?load=effects,controls"></script>

        <script type="text/javascript" src="http://192.168.1.107/static/js/nav/dropdown.js"></script>

        <script type="text/javascript" src="http://192.168.1.107/static/js/modalbox.js"></script>

        <script type="text/javascript" src="http://192.168.1.107/static/js/ajax/main.js"></script>

        <script type="text/javascript" src="http://192.168.1.107/static/js/ajax/autocomplete.js"></script>

        <meta http-equiv= 'pragma' content='no-cache' />

        <meta http-equiv= 'pragma' content='Wed, 11 Jan 1984 05:00:00 GMT' />

        <meta http-equiv= 'Cache-Control' content='no-store, no-cache, must-revalidate' />

        <meta name='robots' content='all' />

        <meta name='author' content='Mallerp Inc. Dev Team' />

        <meta name='description' content='Mallerp Inc.' />

        <link type="image/x-icon" href="http://192.168.1.107/static/images/icons/favicon.ico" rel="shortcut icon" />

        <script type="text/javascript">

            var helper = new Helper();

            helper.periodical_new_task_checker('http://192.168.1.107/index.php/message/fetch_messages/bf2eae46370f74f624d5c7b91ef803f86ec185da');

        </script>

        <script type="text/javascript" src="http://192.168.1.107/static/js/ajax/order.js"></script>
        <script type="text/javascript">
            function  changval(j){
                var unit_price = "unit_price_"+j;
                var quantity ="quantity_"+j;
                var sub_total = "sub_total_"+j;			  
                var array_unit_price = $(unit_price).value;
                var qty = $(quantity).value;
                var sub_total_all = (array_unit_price)*(qty);
                $(sub_total).value = sub_total_all;

                var count = $('count').value;
                var total_t = 0;
                for (i=0;i<count;i++)
                {
                    var sub_total_m = "sub_total_"+i;
                    var total_m = $(sub_total_m).value;
                    total_t = parseInt(total_m) + parseInt(total_t);
                }
                var abc = "abc";
                $(abc).value = total_t;
            }
		 
            function change_sub_total(j){

                var unit_price = "unit_price_"+j;
                var quantity ="quantity_"+j;
                var sub_total = "sub_total_"+j;		  
                var array_unit_price = $(unit_price).value;
                var qty = $(quantity).value;
                var total = $(sub_total).value;
                var unit_price_all = (total)/(qty);
                $(unit_price).value = unit_price_all;
                var count = $('count').value;
                var total_t = 0;
                for (i=0;i<count;i++)
                {
                    var sub_total_m = "sub_total_"+i;
                    var total_m = $(sub_total_m).value;
                    total_t = parseInt(total_m) + parseInt(total_t);
                }
                var abc = "abc";
                $(abc).value = total_t;
            }
		 
        function change_quantity(j){
		 
            var unit_price = "unit_price_"+j;
            var quantity ="quantity_"+j;
            var sub_total = "sub_total_"+j;
            var array_unit_price = $(unit_price).value;
            var qty = $(quantity).value;
            var unit_price_all = $(unit_price).value;
            var total = (unit_price_all)*(qty);
            $(sub_total).value = total;

            var count = $('count').value;
            var total_t = 0;
            for (i=0;i<count;i++)
            {
                var sub_total_m = "sub_total_"+i;
                var total_m = $(sub_total_m).value;
                total_t = parseInt(total_m) + parseInt(total_t);
            }
            var abc = "abc";
            $(abc).value = total_t;
        }
        </script>
    </head>

    <body>
        <div id="wrapper">
            <div id="main">

                <div id="content">

                    <div id="success-msg-top" class="success-msg" style="padding-left: 30px;display:none"></div>

                    <div id="important-top" class="important" style="padding-left: 30px;display:none"></div>
                    <form  action="<?php echo site_url('order/special_order/save_make_pi'); ?>" method="post">
                        <div class="post">
                            <div style="margin:0; padding:10px;"><center>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td><h1 align="center"><strong>COMMERICAL INVOICE</strong></h1></td>
                                        </tr>
                                        <tr>
                                            <td><h3 align="center"><?php echo $order->item_no; ?> </h3></td>
                                        </tr>
                                    </table>

                                    <table width="100%" border="0" cellpadding="0" cellspacing="1" class="tableborder" style="width: 100%;">

                                        <thead>

                                            <tr  class="td">
                                                <td width="5%" height="146"   style="padding-bottom: 10px;padding-top: 10px;"><div align="left"><img src="http://www.b2c-china.com/skin/frontend/default-amazon/a033/images/tomtop_logo.gif" /></div></td>
                                                <td width="45%" height="146"   style="padding-bottom: 10px;padding-top: 10px;"><div align="left"><strong>Seller:<input type="text" style="width:250px;" name="seller" value="<?php echo $user_info->name_en;?>&nbsp;From&nbsp;Yorbay Co.,Ltd"  /></strong><br />
                                                        <br />
                                                        <input type="hidden"  id="itemmm" name="h_id" value="<?php echo $h_id; ?>" />
                                                        <input type="hidden"  id="itemmm" name="itemmm" value="<?php echo $order->item_no; ?>" />
                                                        ADD:<input type="text" id="addr" name="addr" style="width:350px;"  value="Room 1004,HuangYuan Road 1-5# Baiyun district,Guangzhou China " /> <br />
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="addr" name="addr_cn" style="width:350px;" value="广州白云区嘉禾联边工业区尖彭路南2号(伊利雅化妆右侧三楼)" /> <br />
                                                        TEL:<input type="text" id="tel" name="tel" value="<?php echo $user_info->phone; ?>" style="width:150px;" /><br />
                                                        FAX:<input type="text" id="fax" name="fax" value="" style="width:150px;" /><br />
                                                        MOBILE：<input type="text" id="mobile" name="mobile" value="" style="width:150px;" /> <br />
                                                        EMAIL:<input type="text" id="email" name="email" value="<?php echo $user_info->email; ?>" style="width:150px;" /><br />
                                                        WEB:<input type="text" id="web" name="web" value="<?php echo $user_info->platform1; ?>" style="width:150px;" /></div></td>
                                                <td width="50%"  style="padding-bottom: 10px;padding-top: 10px;"><div align="left"><strong>Buyer:<input type="text" name="buyer"  style="width: 250px;"   value="<?php echo $name = $order->name . (empty($order->buyer_id) ? '' : "($order->buyer_id)"); ?>" /> <br />
                                                        </strong><br />
                                                        ADD:<input type="text" id="buy_addr" name="buy_addr" value="<?php echo $buy_addr = $order->address_line_1."&nbsp;". $order->address_line_2."&nbsp;".$order->town_city."&nbsp;".$order->state_province."&nbsp;".$order->country;?>" style="width:350px;" /><br />
                                                        TEL:<input type="text" id="buy_tel" name="buy_tel" value="<?php echo $order->contact_phone_number;?>" style="width:150px;" /> <br />
                                                        FAX:<input type="text" id="buy_fax" name="buy_fax" value="" style="width:150px;" /><br />
                                                        MOBILE:<input type="text" id="buy_mobile" name="buy_mobile" value="" style="width:150px;" /><br />
                                                        EMAIL:<input type="text" id="buy_email" name="buy_email" value="" style="width:150px;" /> <br />
                                                        WEB:<input type="text" id="buy_web" name="buy_web" value="" style="width:150px;" /><br />
                                                        <br />
                                                    </div></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>

                                </center></div>

                            <div style="clear:right;"></div>
                            <table cellspacing="1" cellpadding="0" border="0" class="tableborder" style="width: 100%;">

                                <thead>

                                    <tr>
                                        <th width="9%">SKU</th>
                                        <th width="9%">Photo</th>
                                        <th width="23%">Goods Name <br /></th>
                                        <th width="17%"><div align="center">Unit Price</div></th>
                                        <th width="15%"><div align="center">Currency</div></th>
                                        <th width="15%"><div align="center">Quantity</div></th>
                                        <th width="12%"><div align="center">Sub-total<br />
                                            </div></th>
                                        <th width="15%">Note<br /></th>
                                    </tr>

                                    <?php
                                    $total = array();
                                    $skus = explode(',', $order->sku_str);
                                    $qtys = explode(',', $order->qty_str);
                                    $count = count($skus);

                                    for ($i = 0; $i < $count; $i++) {
                                        if(($skus[$i] == 'X02') OR ($skus[$i] == 'X01')) continue;
                                        $sku_info = $this->order_model->get_purchaser_name_by_sku($skus[$i]);
                                        for ($j = 0; $j < $count; $j++) {
                                            if(($skus[$j] == 'X02') OR ($skus[$j] == 'X01')) continue;
                                            $sku_info2 = $this->order_model->get_purchaser_name_by_sku($skus[$j]);
                                            $total[$j] = ($sku_info2->price) * ($qtys[$j]);
                                        }

                                        if ($count > 1) {
                                            $sum = array_sum($total);
                                            $sub_total = round(($total["$i"] / $sum) * ($order->net), 2);
                                            $unit_price = $sub_total / $qtys[$i];
                                        } else {
                                            $sub_total = $order->net;
                                            $unit_price = ($order->net) / $qtys[$i];
                                        }

                                        $html = "
                                        <tr  class='td'>
                                        <td   style='padding-bottom: 10px;padding-top: 10px;'><input type='text' style='width:100px;' value='$sku_info->sku' name='sku[]' /></td>
                                        <td   style='padding-bottom: 10px;padding-top: 10px;' width='100px';><img src='$sku_info->image_url' height='100' width='100' /> </td>
										 <input type='hidden' value='$sku_info->image_url'  name='sku_img[]'/>
                                        <td  style='padding-bottom: 10px;padding-top: 10px;'><input type='text' style='width:250px;' value='$sku_info->name_en' name='good_name[]' /></td>
                                        <td   style='padding-bottom: 10px;padding-top: 10px;'><div align='center'><input type='text' id='unit_price_{$i}' onchange='changval({$i});' style='width:50px;' value='{$unit_price}' name='unit_price[]' /></div></td>
                                        <td   style='padding-bottom: 10px;padding-top: 10px;'><div align='center'><input type='text' style='width:50px;' value='$order->currency' name='currency[]' /></div></td>
                                        <td   style='padding-bottom: 10px;padding-top: 10px;'><div align='center'><input type='text' onchange='change_quantity({$i});' id='quantity_{$i}' style='width:50px;' value='$qtys[$i]' name='quantity[]' /></div></td>
                                        <td   style='padding-bottom: 10px;padding-top: 10px;'><div align='center'><input type='text' onchange='change_sub_total({$i});' id='sub_total_{$i}' style='width:50px;' value='{$sub_total}' name='sub_total[]' /></div></td>
                                        <td  style='padding-bottom: 10px;padding-top: 10px;'><label>
                                         <textarea name='note_t[]'></textarea>
                                         </label></td>
                                    </tr>";
                                        echo $html;
                                    }
                                    ?>

                                </thead>
                                <tbody>
                                    <tr  class="td">
                                        <td height="34" colspan="5"   style="padding-bottom: 10px;padding-top: 10px;">       <div align="center"></div></td>
                                        <td   style="padding-bottom: 10px;padding-top: 10px;"><div align="right"><h2>Total</h2></div> </td>
                                        <td   style="padding-bottom: 10px;padding-top: 10px;"><div align="center">
                                                <h2><?php echo $order->currency; ?><input type="hidden" id="count" value="<?php echo $count; ?>"><input id="abc" value="<?php echo $order->net; ?>"><input type="hidden" name="total_net" value="<?php echo$order->currency; ?><?php echo $order->net; ?>"/></h2>
                                            </div></td>
                                        <td  style="padding-bottom: 10px;padding-top: 10px;">&nbsp;</td>
                                    </tr>
                                    <tr  class="td">
                                        <td height="34" colspan="8"   style="padding-bottom: 10px;line-height: 20px;padding-top: 10px;">
                                            <textarea rows="4" name="note" cols="140" style="font-size: 13px;">
NOTE:
1.&nbsp;Import duties,taxes and charges are not included in the item price or shipping charges.These charges are the buyer's responsibility.
2.&nbsp;Orders processed within 24-48 hours of payment verification.
3.&nbsp;12 months guarantee date.                                    </textarea></td>
                                    </tr>
                                </tbody>
                            </table>

                            <br />
<!--                            <table width="64%" border="0" cellpadding="0" cellspacing="1" class="tableborder" style="width: 100%;">
                                <tr  class="td">
                                    <td height="98"   style="padding-bottom: 10px;padding-top: 10px;"><h2>Paypal
                                        </h2>
                                        <p> (&lt; 1000USD) </p></td>
                                    <td   style="padding-bottom: 10px;padding-top: 10px;">Paypal Account: wholesale@b2c-china.com (less than USD1000) </td>
                                    <td   style="padding-bottom: 10px;padding-top: 10px;">&nbsp;</td>
                                    <td   style="padding-bottom: 10px;padding-top: 10px;">&nbsp;</td>
                                </tr>
                                <thead>
                                    <tr>
                                        <th colspan="2"><span style="padding-bottom: 10px;padding-top: 10px;">Payment methods</span></th>
                                        <th colspan="2"><span style="padding-bottom: 10px;padding-top: 10px;">Shipping methods</span></th>
                                    </tr>

                                    <tr  class="td">
                                        <td width="10%"   style="padding-bottom: 10px;padding-top: 10px;"><h2>T/T
                                            </h2>
                                            <p> (&gt;200USD) </p></td>
                                        <td width="44%"   style="padding-bottom: 10px;padding-top: 10px;"> Beneficiary  Bank: HSBC HongKong<br />
                                            Account: 808 402 127 838<br />
                                            Beneficiary: MallErp GROUP LIMITED<br />
                                            SWIFT CODE: HSBCHKHH<br />
                                            Bank Address: 6F, HSBC Main Building 1# queen's Road Central, Hong Kong </td>
                                        <td width="9%" height="90"   style="padding-bottom: 10px;padding-top: 10px;">DHL</td>
                                        <td width="37%"   style="padding-bottom: 10px;padding-top: 10px;">&nbsp;</td>
                                    </tr>
                                    <tr  class="td">
                                        <td   style="padding-bottom: 10px;padding-top: 10px;"><h2>WestUnion  </h2>(&gt;200USD)</td>
                                        <td   style="padding-bottom: 10px;padding-top: 10px;"> Beneficiary: XiaoxiaGuo <br />
                                            First Name: Xiaoxia <br />
                                            Last Name: Guo <br />
                                            Country: China <br />
                                            Address: 4/F, No. A2 building HeKan Industrial Park, Bantian, LongGang District, Shenzhen,Guangdong, China  </td>
                                        <td height="34"   style="padding-bottom: 10px;padding-top: 10px;">Delivery terms:</td>
                                        <td   style="padding-bottom: 10px;padding-top: 10px;">3-15 Days</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>-->
<!--                            <br />-->
                            <table width="100%" border="0" cellpadding="0" cellspacing="1" class="tableborder" style="width: 100%;">
                                <thead>
                                    <tr  class="td">
                                        <td width="47%" height="51" valign="bottom"   style="padding-bottom: 10px;padding-top: 10px;"><strong>Seller:Confirmed by ______________________ Date:<?php echo "<input type='text' style='width:100px;' value='{$date}' name='date' />" ?> </strong></td>
                                        <td width="53%" valign="bottom"  style="padding-bottom: 10px;padding-top: 10px;"><strong>Buyer:Confirmed by</strong>______________________ <strong>Date: __________</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="red"><input type="submit" value="save" /></span></td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <br />

                            <div style="float: right; "></div>
                            <div style="clear:both;"></div>
                        </div>
                    </form>
                    <div id="success-msg-foot" class="success-msg" style="padding-left: 30px;display:none"></div>

                    <div id="important-foot" class="important" style="padding-left: 30px;display:none"></div>

                    <div id="message_popup" style="right: 0px; top: 38px; position: absolute; display: none">

                    </div>

                    <div style="left: -2px; top: 0px; width: 1423px; height: 754px;display: none; " id="loading-mask">

                        <p id="loading_mask_loader" class="loader"><img alt="Loading..." src="http://192.168.1.107/static/images/ajax-loader-tr.gif" /><br/>Please wait...</p>

                    </div>

                </div>

            </div>

        </div>

        <script type="text/javascript">

        var menu=new menu.dd("menu");

        menu.init("menu","menuhover");

        </script>

    </body>

</html>