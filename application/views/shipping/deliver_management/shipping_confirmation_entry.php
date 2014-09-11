<?php
$scan_bar_code = lang('scan_bar_code');
$enter_item_no = lang('enter_item_no');
$submit = lang('submit');
$wait_for_shipping_confirmation = lang('wait_for_shipping_confirmation');
$epacket_confirmation = lang('epacket_wait_for_enter_weight');
$shipping_confirmation = lang('shipping_confirmation');
$html =<<< HTML
<center>
<div align="center" style="
    background-color: #E0F0FE;
    border: 1px solid #8CC7FB;
    padding: 1px 10px 10px 10px;
    width: 300px;">
    <h2>$shipping_confirmation</h2><br/>
    <form>
    <input type="text" value="" size="20" name="bar_code" id="bar_code"> $scan_bar_code
	<br/><br/>
    <input type="text" value="" size="20" name="item_no" id="item_no"> $enter_item_no
    <br/><br/>
    <input type="submit" value="$submit" name="submit" id="submit" onClick="return false;">
    </form>
    <br/><br/>
    $wait_for_shipping_confirmation: $wait_confirmation_count <br/>
    $epacket_confirmation: $epacket_count
</div>
</center>
HTML;

echo $html;
$url = site_url('shipping/deliver_management/before_make_order_shipped');

?>

<script>
    document.observe('dom:loaded', function() {
        if ($('bar_code'))
        {
            $('bar_code').focus();
            $('submit').observe('click', function() {
                before_confirm_shipping("<?=$url?>");
            });
        }
    });
</script>