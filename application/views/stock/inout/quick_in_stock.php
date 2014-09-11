<?php
$instock_num = lang('instock_num');
$sku = lang('sku');
$note = lang('note');
$submit = lang('submit');
$shipping_confirmation = lang('quick_in_stock');
$html =<<< HTML
<center>
<div align="center" style="
    background-color: #E0F0BE;
    border: 1px solid #8CC7FB;
    padding: 1px 10px 10px 10px;
    width: 300px;">
    <h2>$shipping_confirmation</h2><br/>
    <form>
    $instock_num :<input type="text" value="" size="20" name="qty" id="qty">
	<br/><br/>
    $sku :<input type="text" value="" size="20" name="sku" id="sku"> 
    <br/><br/>
	$note :<input type="text" value="" size="20" name="note" id="note"> 
	<br/><br/>
    <input type="submit" value="$submit" name="submit" id="submit" onClick="return false;">
    </form>
    <br/><br/>
</div>
</center>
HTML;

echo $html;
$url = site_url('stock/inout/save_quick_in_stock');

?>

<script>
    document.observe('dom:loaded', function() {
        if ($('qty'))
        {
            $('qty').focus();
            $('submit').observe('click', function() {
                quick_in_stock("<?=$url?>");
            });
        }
    });
function quick_in_stock(url)
{
    var params = $H({});

    if ($('qty').value.strip()&&$('sku').value.strip())
    {
        params.set('qty', $('qty').value.strip());
		params.set('sku', $('sku').value.strip());
    }
    else
    {
        alert('input sku and qty!');
        return false;
    }
    helper.update_content(url, params);
    
    return true;
}
</script>