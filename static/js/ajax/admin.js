
function save_shipped_notification_template(url)
{
    var params = $H({});
    params.set('template_content', $('edit_template').value);

    return helper.ajax(url, params, 1);
}

