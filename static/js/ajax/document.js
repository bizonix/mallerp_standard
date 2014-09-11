function extand_catalog(cat_ids)
{
    var last;
    $A(cat_ids).each(function (e) {
        last = e;
        var parent = $('catalog_' + e).parentNode.parentNode;
        var id = $(parent).identify();
        
        if ($(id + '-body'))
        {
            $(id + '-body').show();
        }
    });
    $('catalog_' + last).checked = true;
}

function save_provider_name(url, params)
{
    if ($(id).strip().empty())
    {
        return false;
    }
    return ajax(url, params);
}

function submit_content(element, url, new_url, clue)
{
    var form = $H($('document_content_form').serialize(true));

    if ($('document_content'))
    {
        form = form.merge({document_content : tinyMCE.get('document_content').getContent()});
    }
    element.blur();

    return helper.ajax_redirect_for_document(url, form, 'post', new_url, clue);
}

function submit_content_edit(element, url)
{
    var form = $H($('document_content_form').serialize(true));

    if ($('document_content'))
    {
        form = form.merge({document_content : tinyMCE.get('document_content').getContent()});
    }
    element.blur();

    return helper.ajax(url, form, 1);
//    return helper.update_content(url, form, 'upload_html');
}


function submit_comment(element, url)
{
    var form = $H($('document_comment_form').serialize(true));

    if ($('document_comment'))
    {
        form = form.merge({document_comment : tinyMCE.get('document_comment').getContent()});
    }
    element.blur();

    return helper.update_content(url, form, 'current_comment_div');
}


function delete_comment(url)
{
    return helper.update_content(url, null, 'current_comment_div');
}


function delete_file(url)
{
    return helper.update_content(url, null, 'current_file_div');
}