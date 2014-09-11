<?php

$html = '';
foreach ($orders as $order)
{
    $content = create_print_label_content($order);

    $html .=<<< HTML
<table cellspacing="0" cellpadding="3" border="1" align="center" class="label">
<tbody>
    <tr align="left">
        <td style="line-height: 28px;">
            <font size="5" face="3 of 9 barcode" style="font-size: 26px;">
                *$order->id*
            </font>  * $order->id * <br>
            <textarea rows="15" cols="70" name="label_content_$order->id" id="label_content_$order->id">
$content
            </textarea>
        </td>
    </tr>
</tbody>
</table>
<input type="hidden" name="order_id_$order->id" value="$order->id" id="order_id_$order->id"  />
HTML;
}
echo $html;
$base_url = base_url();
echo <<<LOADING
<div style="left: -2px; top: 0px; width: 1423px; height: 754px;display: none; " id="loading-mask">
    <p id="loading_mask_loader" class="loader"><img alt="Loading..." src="{$base_url}static/images/ajax-loader-tr.gif"><br>Please wait...</p>
</div>
LOADING;

$print_label_url = site_url('shipping/deliver_management/print_label');
$config = array(
    'name'      => 'print_label',
    'id'        => 'print_label',
    'value'     => lang('next_step_start_print_label'),
    'type'      => 'button',
    'onclick'   => "print_label('$print_label_url');",
);

$print_label = '<center style="margin: 10px;">';
$print_label .= form_input($config);
$print_label .= '</center>';
echo $print_label;

?>