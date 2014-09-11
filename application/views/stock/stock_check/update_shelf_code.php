<?php
$shelf_code = lang('shelf_code');
$sku = lang('sku');
$submit = lang('submit');
$shipping_confirmation = lang('update_shelf_code');
$html =<<< HTML
<center>
<div align="center" style="
    background-color: #E0F0BE;
    border: 1px solid #8CC7FB;
    padding: 1px 10px 10px 10px;
    width: 300px;">
    <h2>$shipping_confirmation</h2><br/>
    <form>
    $shelf_code :<input type="text" value="" size="20" name="shelf_code" id="shelf_code">
	<br/><br/>
    $sku :<input type="text" value="" size="20" name="sku" id="sku"> 
    <br/><br/>
    <input type="submit" value="$submit" name="submit" id="submit" onClick="return false;">
    </form>
    <br/><br/>
</div>
</center>
HTML;

echo $html;
$url = site_url('stock/stock_check/save_update_shelf_code');

?>

<script>
    document.observe('dom:loaded', function() {
        if ($('shelf_code'))
        {
            $('shelf_code').focus();
            $('submit').observe('click', function() {
                quick_in_stock("<?=$url?>");
            });
        }
    });
function quick_in_stock(url)
{
    var params = $H({});

    if ($('shelf_code').value.strip()&&$('sku').value.strip())
    {
        params.set('shelf_code', $('shelf_code').value.strip());
		params.set('sku', $('sku').value.strip());
    }
    else
    {
        alert('input sku and shelf_code!');
        return false;
    }
    helper.update_content(url, params);
    
    return true;
}
</script>