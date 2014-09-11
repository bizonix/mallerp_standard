<?php

function create_price_make_pi($data) {

    $html = <<< HTML

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">

    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Mallerp management system</title>

        <style type='text/css' media='all'>@import url('http://192.168.1.107/static/css/main.css');</style>

        <style type='text/css' media='all'>@import url('http://192.168.1.107/static/css/nav/dropdown.css');</style>

        <style type='text/css' media='all'>@import url('http://192.168.1.107/static/css/modalbox.css');</style>

        <script type="text/javascript" src="http://192.168.1.107/static/js/lib/prototype.js"></script>

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

        <link type="image/x-icon" href="http://192.168.1.107/static/images/icons/favicon.ico" rel="shortcut icon">
        <style>
            td {font-size:10px;background:#ffffff;}
            #content h2 {
                background-color: transparent;
                color: #000000;
                font-size: 12px;
            }
            #content h1 {
                color: #E13300;
                font-size: 20px;
                font-weight: normal;
            }
            #content th {
                font-size: 10px;
                font-weight: bold;
                text-align: left;
            }
        </style>


        <script type="text/javascript">

            var helper = new Helper();

            helper.periodical_new_task_checker('http://192.168.1.107/index.php/message/fetch_messages/bf2eae46370f74f624d5c7b91ef803f86ec185da');

        </script>

        <script type="text/javascript" src="http://192.168.1.107/static/js/ajax/order.js"></script>
    </head>

    <body>
    <div id="wrapper">
      <div id="main">

                <div id="content">

                    <div id="success-msg-top" class="success-msg" style="padding-left: 30px;display:none"></div>

                                        <div id="important-top" class="important" style="padding-left: 30px;display:none"></div>

                                        <div class="post" >
                                          <div style="margin:0; padding:10px;"><center>
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><h1 align="center"><strong>COMMERICAL INVOICE</strong></h1></td>
                            </tr>
                            <tr>
                              <td><h3 align="center"> </h3></td>
                            </tr>
                          </table>
                          <table width="100%" border="0" cellpadding="0" cellspacing="1" class="tableborder" style="width: 100%;">

                            <thead>

                              <tr  class="td">
HTML;
                    if( ! in_array('X01', $data['sku'])) {
                        $html .= '<td width="5%" height="146"   style="padding-bottom: 10px;padding-top: 10px;"><div align="left"><img style="height:50px;" src="http://www.b2c-china.com/skin/frontend/default-amazon/a033/images/tomtop_logo.gif" /></div></td>';
                    }
                    $html .= <<< HTML
                                <td width="45%" height="146"   style="padding-bottom: 10px;padding-top: 10px;"><div align="left"><strong>Seller:{$data['seller']}</strong><br />
                                        <br />
                                  ADD:{$data['addr']} <br />
                                  TEL:{$data['tel']}<br />
                                  FAX:{$data['fax']}<br />
                                  MOBILE:{$data['mobile']}<br />
                                  EMAIL:{$data['email']}<br />
                                WEB:<a href="{$data['web']}" target="_blank">{$data['web']}</a></div></td>
                                <td width="50%"  style="padding-bottom: 10px;padding-top: 10px;"><div align="left"><strong>Buyer:{$data['Buyer']} <br />
                                    </strong><br />
                                  ADD:{$data['buy_addr']} <br />
                                  TEL:{$data['buy_tel']} <br />
                                  FAX:{$data['buy_fax']}<br />
                                  MOBILE:{$data['buy_mobile']}<br />
                                  EMAIL:{$data['buy_email']} <br />
                                  WEB:<a href="{$data['buy_web']}" target="_blank">{$data['buy_web']}</a><br />
                                  <br /><br />
                                </div></td>
                              </tr>
                            </thead>
                            <tbody>
                            </tbody>
                          </table>
                        </center></div>
                        <form action="http://192.168.1.107/index.php" method="post" accept-charset="utf-8">
  <div style="clear:right;"></div>
  <table cellspacing="1" cellpadding="1" bgcolor="#666" border="0" class="tableborder" style="width: 100%;border:0px;">

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

HTML;
    $total_money = '';
    for ($i = 0; $i < $data['count']; $i++) {
        $show_sku["$i"] = element($i, $data['sku']);
        $show_sku_img["$i"] = element($i, $data['sku_img']);
        $show_good_name["$i"] = element($i, $data["good_name"]);
        $show_unit_price["$i"] = element($i, $data["unit_price"]);
        $show_currency["$i"] = element($i, $data['currency']);
        $show_quantity["$i"] = element($i, $data['quantity']);
        $show_note["$i"] = element($i, $data["note_t"]);
        $sub_total["$i"] = element($i, $data['sub_total']);
        $total_money += $sub_total["$i"];

        

        $html .=<<< HTML
        <tr  class="td" border="1">
        <td height="34"   style="padding-bottom: 10px;padding-top: 10px;">{$show_sku["$i"]}</td>
        <td   style="padding-bottom: 10px;padding-top: 10px;"><img src='{$show_sku_img["$i"]}' height='50' width='50' /> </td>
        <td  style="padding-bottom: 10px;padding-top: 10px;">{$show_good_name["$i"]}</td>
        <td   style="padding-bottom: 10px;padding-top: 10px;"><div align="center">{$show_unit_price["$i"]}</div></td>
        <td   style="padding-bottom: 10px;padding-top: 10px;"><div align="center">{$show_currency["$i"]}</div></td>
        <td   style="padding-bottom: 10px;padding-top: 10px;"><div align="center">{$show_quantity["$i"]}</div></td>
        <td   style="padding-bottom: 10px;padding-top: 10px;"><div align="center">{$sub_total["$i"]}</div></td>
        <td  style="padding-bottom: 10px;padding-top: 10px;">{$show_note["$i"]}</td>
        </tr>

HTML;
    }

    $html .=<<<HTML
    </thead>
        <tr  class="td">
      <td height="34" colspan="5"   style="padding-bottom: 10px;padding-top: 10px;">
      <div align="center"></div></td>
      <td   style="padding-bottom: 10px;padding-top: 10px;"><div align="right"><strong><h2>Total</h2></strong></div></td>
      <td   style="padding-bottom: 10px;padding-top: 10px;"><div align="center">
        <h2>{$total_money}</h2>
      </div></td>
      <td  style="padding-bottom: 10px;padding-top: 10px;">&nbsp;</td>
      </tr>
    <tr  class="td">
      <td height="34" colspan="8"   style="padding-bottom: 10px;line-height:20px;padding-top: 0px;">
        {$data['note']}
        </td>
      </tr>
    <tbody>
    </tbody>
  </table>
  <br />
 
  <table width="100%" border="0" cellpadding="0" cellspacing="1" class="tableborder" style="width: 100%;">
    <thead>
      <tr  class="td">
        <td width="47%" height="51" valign="bottom"   style="padding-bottom: 10px;padding-top: 10px;"><strong>Seller:Confirmed by ________________ Date:&nbsp;<U>&nbsp;{$data['date']}&nbsp;</U></strong></td>
        <td width="53%" valign="bottom"  style="padding-bottom: 10px;padding-top: 10px;"><strong>Buyer:Confirmed by ________________ Date: __________</strong></td>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
  <br />
                        </form>
                        <div style="float: right; "></div>
<div style="clear:both;"></div>
                                        </div>
                                        <div id="success-msg-foot" class="success-msg" style="padding-left: 30px;display:none"></div>

                                        <div id="important-foot" class="important" style="padding-left: 30px;display:none"></div>

                                        <div id="message_popup" style="right: 0px; top: 38px; position: absolute; display: none">

                    </div>

                    <div style="left: -2px; top: 0px; width: 1423px; height: 754px;display: none; " id="loading-mask">

                        <p id="loading_mask_loader" class="loader"><img alt="Loading..." src="http://192.168.1.107/static/images/ajax-loader-tr.gif"><br>Please wait...</p>

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
HTML;
    $show_time = time();
    $contact = '/var/www/html/mallerp/static/before_order_pi/';
    $path = $contact . "{$data['user_id']}" . "-" . $show_time . '.html';
    file_put_contents($path, $html);
}
?>
