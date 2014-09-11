    
    
//    ajax_redirect: function(url, params, method)
function ajax_redirect_qt(url, params,redirect_url)
{
    new Ajax.Request(url, {
        parameters: params,
        onSuccess: function (transport) {
            
            var response = $H(eval(transport.responseJSON));
            if (response.get('status')) {
                alert(response.get('msg'));
                location.href = redirect_url;
            } else {
                alert(response.get('msg'));
            }
        },

        onFailure: (function (transport) {
            this._failure(transport.responseText);
        }).bind(this)
    });
}