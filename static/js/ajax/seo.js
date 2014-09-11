
function submit_release(url, resource_id,content_id, tag, link)
{
    if (tag == 'drop')
    {
        if ( ! confirm('Are you sure?'))
        {
            return;
        }
        var hash = $H({resource_id: resource_id,content_id:content_id,tag: tag});
    }
    if(tag == 'save')
    {
    var validate_url = $('validate_url_' + resource_id).value;

    if ( ! validate_url)
    {
        alert('Varification url is required');
        return ;
    }
    var hash = $H({resource_id: resource_id,content_id:content_id,tag: tag, validate_url:validate_url});
    }
    if (helper.ajax(url, hash, 1))
    {
        $(link.parentNode.parentNode).remove();
    }

}


function handwork_validate(current, url, resource_id, last,tag)
{
    if(tag)
    {
        if ( ! confirm('Are you sure?'))
        {
            return false;
        }
    }

    var params = $H({'resource_id': resource_id});
//    var note = $('note_' + order_id).value.strip();
//    params.set('note', note);
    if (last == 1)
    {
        helper.ajax_reflesh(url, params);
    }
    else
    {
        helper.drop_row(current, '', url, params);
    }
    return false;
}

function filter_company_resource(url, url_key, content_id, value)
{

    url = url + content_id + '/'+ value + '/' + url_key;
    window.location.href = url;
}

function batch_to_add_interal(url, type)
{
    var selects = $$('input[id^="select_" type=checkbox]');
    var params = $H({});
    var content_count = 0;
    var error = false;
    selects.each(function(e)
    {
        if (e.checked)
        {
            var content_id = e.value;
            params.set('content_id_' + content_count, content_id);
            if ($('integral_' + content_id))
            {
                var integral = $('integral_' + content_id).value;
            }

            params.set('integral_' + content_count, integral);
            content_count++;
        }
    });

    if (error)
    {
        alert(error);
        return false;
    }

    if (content_count == 0)
    {
        alert('no item selected!');
        return false;
    }

    params.set('content_count', content_count);
    params.set('type', type);

    helper.ajax_reflesh(url, params, 'post', 1);

    return true;
}

function batch_to_add_resource_interal(url, type)
{
    var selects = $$('input[id^="select_" type=checkbox]');
    var params = $H({});
    var resource_count = 0;
    var error = false;
    selects.each(function(e)
    {
        if (e.checked)
        {
            var resource_id = e.value;
            params.set('resource_id_' + resource_count, resource_id);
            if ($('integral_' + resource_id))
            {
                var integral = $('integral_' + resource_id).value;
            }

            params.set('integral_' + resource_count, integral);
            resource_count++;
        }
    });

    if (error)
    {
        alert(error);
        return false;
    }

    if (resource_count == 0)
    {
        alert('no item selected!');
        return false;
    }

    params.set('resource_count', resource_count);
    params.set('type', type);

    helper.ajax_reflesh(url, params, 'post', 1);

    return true;

}

function save_email_template(url,id)
{
    var params = $H({});
    params.set('id', id);
    params.set('title', $('mail_title').value);
    params.set('code', $('mail_code').value);
    params.set('content', $('mail_content').value);
    return helper.ajax(url, params, 1);
}

function mail_content_show_hidden(id)
{
    var status = document.getElementById("content_view_"+id).style.display;
    if(status == "none")
    {
        document.getElementById("content_view_"+id).style.display = "block";
        document.getElementById("img_"+id).innerHTML  = " - ";
    }
    else
    {
        document.getElementById("content_view_"+id).style.display = "none";
        document.getElementById("img_"+id).innerHTML  = " + ";
    }
}
