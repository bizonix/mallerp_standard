function checkbox(url, message_id, read, group_name)
{
    var params = $H({});
    params.set('message_id', message_id);
    params.set('read', read);
    params.set('group_name', group_name);
    helper.ajax_reflesh(url, params, 'post',1);
    return false;
}


