function submit_content(element, url)
{
    var form = $H($('product_form').serialize(true));

    var keys = form.keys();
    if (keys.indexOf('description') != -1)
    {
        form = form.merge({description: tinyMCE.get('description').getContent()});
    }
    if (keys.indexOf('short_description') != -1)
    {
        form = form.merge({short_description: tinyMCE.get('short_description').getContent()});
    }
    if (keys.indexOf('description_cn') != -1)
    {
        form = form.merge({description_cn: tinyMCE.get('description_cn').getContent()});
    }
    if (keys.indexOf('short_description_cn') != -1)
    {
        form = form.merge({short_description_cn: tinyMCE.get('short_description_cn').getContent()});
    }
    element.blur();
    
    return helper.ajax(url, form, 1);
}

function extand_catalog(cat_ids)
{
    var last;
    $A(cat_ids).each(function (e) {
        last = e;
        if ($('catalog_' + e))
        {
            var parent = $('catalog_' + e).parentNode.parentNode;
            var id = $(parent).identify();

            if ($(id + '-body'))
            {
                $(id + '-body').show();
            }
        }
    });
    if ($('catalog_' + last))
    {
        $('catalog_' + last).checked = true;
    }
}

function save_provider_name(url, params)
{
    if ($(id).strip().empty())
    {
        return false;
    }
    return ajax(url, params);
}

function get_packing(object,url)
{
    param = {'id' : object.value};
    return helper.update_content(url, param, 'packing_show'); 
}