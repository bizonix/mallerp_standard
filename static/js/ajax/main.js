
var Helper = Class.create({
    initialize: function() {
        this.success_msg_align = 'left';
        this.important_msg_align = 'left';
        this.response_value = '';
        this._message_top = '';
        this.page_limit = 20;
    },
    ajax: function(url, params, modalbox) {
        var successful = true;
        new Ajax.Request(url, {
            parameters: params,
            onSuccess: (function (transport) {
                var response = $H(eval(transport.responseJSON));
                if (response.get('status')) {
                    this._success(response.get('msg'));
                } else {
                    this._failure(response.get('msg'));
                }
                if (modalbox) {
                    this.hide_loading();
                }
            }).bind(this),
            onFailure: (function (transport) {
                this._failure(transport.responseText);

                if (modalbox) {
                    this.hide_loading();
                }
                successful = false;
            }).bind(this)
        });
        if (modalbox) {
            this.show_loading();
        }
        return successful;
    },
    _success: function(text) {
      $('success-msg-top','success-msg-foot').invoke('update',text);
      $('success-msg-top').align = this.success_msg_align;
      $('success-msg-foot').align  = this.success_msg_align;
      $('success-msg-top','success-msg-foot').invoke('show');
      if (this.success_msg_align == 'left') {
          this.success_msg_align = 'right';
      } else {
          this.success_msg_align = 'left';
      }
      $('important-top','important-foot').invoke('hide');
    },

    hide_failure: function() {
        $('important-top','important-foot').invoke('hide');
    },
    _failure: function(text) {
        $('important-top','important-foot').invoke('update',text);
        $('important-top').align = this.important_msg_align;
        $('important-foot').align = this.important_msg_align;
        $('important-top','important-foot').invoke('show');
        if (this.important_msg_align == 'left') {
          this.important_msg_align = 'right';
        } else {
          this.important_msg_align = 'left';
        }
        $('success-msg-top','success-msg-foot').invoke('hide');
    },
    editor: function(id, url, form_id, params, collection, create) {
        if ($A(collection).size()) {
            new Ajax.InPlaceCollectionEditor(
                id,
                url,
                {
                    formId: form_id,
                    collection: $A(collection),
                    ajaxOptions: {
                        parameters: params,
                        onSuccess: (function(transport) {
                            var response = $H(eval(transport.responseJSON));

                            if (response.get('status')) {
                                this._success(response.get('msg'));
                            } else {
                                this._failure(response.get('msg'));
                            }
                            this.response_value = response.get('value') || '!$!';
                        }).bind(this)
                    },
                    onComplete: (function () {
                        if (this.response_value) {
                            if (this.response_value == '!$!') {
                                $(id).update('');
                            } else {
                                $(id).update(this.response_value);
                            }
                            this.response_value = '';
                        }
                    }).bind(this),

                    onFailure: (function () {
                        this._failure('Server error!');
                    }).bind(this),
                    
                    onLeaveHover: function() {
                            // do nothing - this is a fix for javascript error in IE with scriptaculous 1.8.1
                          }

                }
            );
        }
        else
        {
            new Ajax.InPlaceEditor(
                id,
                url,
                {
                    formId: form_id,
                    ajaxOptions: {
                        parameters: params,
                        onSuccess: (function(transport) {
                            var response = $H(eval(transport.responseJSON));

                            if (response.get('status')) {
                                this._success(response.get('msg'));
                            } else {
                                this._failure(response.get('msg'));
                            }
                                this.response_value = response.get('value') || '!$!';
                        }).bind(this)
                    },
                    onComplete: (function () {
                        if (this.response_value) {
                            if (this.response_value == '!$!') {
                                $(id).update('');
                            } else {
                                $(id).update(this.response_value);
                            }
                            this.response_value = '';
                        }
                    }).bind(this),

                    onCreate: create || Prototype.emptyFunction,

                    onFailure: (function () {
                      this._failure('Server error!');
                    }).bind(this)

                }
            );
        }
    },
    add_row: function(url, params)
    {
        return this.ajax_reflesh(url, params);
    },

    ajax_reflesh: function(url, params, method, notice)
    {
        if (! method)
        {
            method = 'post';
        }
        new Ajax.Request(url, {
            parameters: params,
            onSuccess: function (transport) {
                var response = $H(eval(transport.responseJSON));
                if (response.get('msg') && response.get('status') == 0)
                {
                    alert(response.get('msg'));
                }
                else
                {
                    if (notice)
                    {
                        alert('OK!');
                    }
                }
                location.reload();
            },
            method: method,
            onFailure: (function (transport) {
                this._failure(transport.responseText);
            }).bind(this)
        });
    },
    ajax_redirect: function(url, params, method)
    {
        if (! method)
        {
            method = 'post';
        }
        new Ajax.Request(url, {
            parameters: params,
            onSuccess: function () {
                location.href = url;
            },
            method: method,
            onFailure: (function (transport) {
                this._failure(transport.responseText);
            }).bind(this)
        });
    },
    drop_row: function(current, msg, url, params, removeNode)
    {
        if ( ! msg || confirm(msg))
        {
            if ( ! removeNode)
            {
                removeNode = $(current.parentNode.parentNode);
            }
            new Ajax.Request(url, {
                parameters: params || {},
                onSuccess: (function (transport) {
                    var response = $H(eval(transport.responseJSON));

                    if (response.get('status')) {
                        $(current).remove();
                        $(removeNode).remove();
                        this._success(response.get('msg'));
                    } else {
                        this._failure(response.get('msg'));
                    }
                    this.hide_loading();
                }).bind(this),
                onFailure: (function (transport) {
                    this._failure(transport.responseText);
                    this.hide_loading();
                }).bind(this)
        });
        }
    },
    modal: function (object, title, width)
    {
        if (object)
        {
            if ( ! title)
            {
                title = '';
            }
            if ( ! width)
            {
                width = 1000;
            }
            object.blur();
            Modalbox.show(
                object.href,
                {
                    title: title,
                    width: width
                    //height: 300,
                    //afterHide: function() {window.location.reload();}
                }
            );
            return false;
        }

        return true;
    },   
    change_lang: function (url, lang)
    {
        if (lang == 0)
        {
            return false;
        }
        return this.ajax_reflesh(url, {language: lang})
    },
    periodical_new_task_checker: function(url, params)
    {
        if ( ! params)
        {
            params = {};
        }
        new PeriodicalExecuter((function(pe) {
            new Ajax.Request(url, {
                parameters: params,
                onSuccess: (function (transport) {
                    var response = $A(eval(transport.responseJSON));
                    new_message_pop = this.new_message_pop;
                    
                    if (response.size()) {
                        response.each((function (e){
                            message = $H(e);
                            new_message_pop(
                                message.get('title'),
                                message.get('content'),
                                message.get('click_url'),
                                message.get('close')
                            );
                        }).bind(this));
                    }
                }).bind(this)
            });
            this._set_top();
            window.onscroll = this._set_top;
            window.onresize = this._set_top;
        }).bind(this), 30);

        return true;
    },
    new_message_pop: function (title, content, url, close)
    {
        var message = '<div style="display: block;" class="embedFirstTips">';
        message += '<div class="tipsContent">'
        message += '<div class="ico"></div>'
        message += '<div class="title">' + title + '<br><a target="_blank" href="' + url + '">' + content + '</a></div>'
        message += '<div class="close"><a style="cursor: pointer" onclick="Effect.Fade($(this.parentNode.parentNode.parentNode));" id="embedTipsClose">I know!</a></div>'
        message += '</div>'
        message += '</div>';
        if ($('message_popup'))
        {
            $('message_popup').insert(message);
            Effect.Appear('message_popup', {duration: 2.0});
        }
    },
    _set_top: function ()
    {
        var scrollTop=0;
        if(document.documentElement&&document.documentElement.scrollTop)
        {
            scrollTop=document.documentElement.scrollTop;
        }
        else if(document.body)
        {
            scrollTop=document.body.scrollTop;
        }
        if (scrollTop < 38)
        {
            scrollTop = 38;
        }
        var _message_top = '' + scrollTop + 'px';
        if ($('message_popup'))
        {
            $('message_popup').setStyle({top: _message_top});
        }
    },
    check_all: function()
    {
        var selects = $$('input[id^="checkbox_select_"]');
        selects.each(function(e){
            e.checked = 1;
        });
    },
    uncheck_all: function()
    {
        var selects = $$('input[id^="checkbox_select_"]');
        selects.each(function(e) {
            e.checked = 0;
        });
    },
    check_group: function(group_name)
    {
        var selects = $$('input[name="' + group_name + '"]');
        selects.each(function(e){
            e.checked = 1;
        });
    },
    uncheck_group: function(group_name)
    {
        var selects = $$('input[name="' + group_name + '"]');
        selects.each(function(e){
            e.checked = 0;
        });
    },
    reset_or_search: function(url, params, content_id)
    {
        this.show_loading();
        params = $H(params);
        params.set('set_limit_page', this.page_limit);
        if (content_id)
        {
            return this.update_content(url, params, content_id);
        }
        
        return this.ajax_redirect(url, params);
    },

    sort_table: function(sort_url, key)
    {
        this.show_loading();
        params = $H({sort_key: key});
        return this.ajax_redirect(sort_url, params);
    },
    set_page_limit: function(value)
    {
        this.page_limit = value;
    },
    cancel_icon: function(base_url, onclick)
    {
        var html = '<span style="cursor:pointer;"';
        html += 'onclick="' + onclick + '">';
        html += '<img src="' + base_url + 'static/images/icons/cancel.gif"/>';
        html += '</span>';

        return html;
    },
    show_loading: function()
    {
        var body_array = $$('body');
        var body = body_array[0];
        var height = body.getHeight();
        if ( ! $('loading-mask'))
        {
            var loading = '<div style="left: -2px; top: 0px; width: 1423px; height: 754px;display: none; " id="loading-mask">';
            loading += '<p id="loading_mask_loader" class="loader"><img alt="Loading..." src="/static/images/ajax-loader-tr.gif"><br>Please wait...</p>';
            loading += '</div>';
            body.insert(loading);
        }
        $('loading-mask').setStyle({height: height + 'px'});
        $('loading-mask').show();
    },
    hide_loading: function()
    {
        $('loading-mask').hide();
    },
    update_content: function(url, params, id, no_loading)
    {
        if ( ! id)
        {
            id = 'content';
        }
        if (no_loading)
        {
            this.show_loading();
        }
        new Ajax.Request(url, {
            parameters: params,
            onSuccess: (function (transport) {
                var response = $H(eval(transport.responseJSON));
                if (response.get('msg'))
                {
                    if (response.get('status')) {
                        this._success(response.get('msg'));
                    } else {
                        this._failure(response.get('msg'));
                    }
                }
                else
                {
                    response = transport.responseText;
                    $(id).update(response);
                    $(id).show();
                }
                helper.hide_loading();
            }).bind(this),
            onFailure: function () {
                helper.hide_loading();
                alert('unknown error, try again!');
            }
        });
    },
    ajax_toggle_content: function(url, params, show_content_id, hide_content_id)
    {
        this.show_loading();
        new Ajax.Request(url, {
            parameters: params,
            onSuccess: function (transport) {
                helper.hide_loading();

                var response = $H(eval(transport.responseJSON));
                if (response.get('status') == 0 && response.get('msg')) {
                    alert(response.get('msg'));

                    return false;
                }
                
                var response = transport.responseText;
                if (hide_content_id)
                {
                    $(hide_content_id).hide();
                }
                $(show_content_id).update(response);
                $(show_content_id).show();
            },
            onFailure: function () {
                helper.hide_loading();
                alert('unknown error, try again!');
            }
        });
    },
    toggle_content: function (content_1, content_2)
    {
        $(content_1).toggle();
        $(content_2).toggle();
    },
    show_update: function (show_block_id, update_id, show_update_content, hide_update_content)
    {
        $(show_block_id).toggle();
        if ($(show_block_id).visible())
        {
            $(update_id).update(show_update_content);
        }
        else
        {
            $(update_id).update(hide_update_content);
        }
    },
    is_num: function (value)
    {
        var pattern = /^\d+\.\d+|\d+$/;

        return pattern.test(value);
    },
    focus: function (id)
    {
        if ($(id))
        {
            $(id).focus();
            $(id).select();
        }
    },
    toggle_parent_next: function(el, level, css_class)
    {
        var parent = $(el.parentNode);
        if (level)
        {
            for (var i = 1; i < level; i++)
            {
                parent = $(parent.parentNode);
            }
        }
        parent.next().toggle();
        if (css_class)
        {
            $(el.toggleClassName(css_class));
        }

        return false;
    },
    copy_to_clipboard: function (text)
    {
        window. clipboardData.setData('Text', text );
    },
    check_session: function (transport)
    {
        var response = $H(eval(transport.responseJSON));
        if (response.get('status') == 0 && response.get('msg')) {
            alert(response.get('msg'));

            return false;
        }

        return true;
    },    
    ajax_redirect_for_document: function(url, params, method, new_url, clue)
    {
        if (! method)
        {
            method = 'post';
        }
        if ( ! new_url)
        {
            new_url = url;
        }
        new Ajax.Request(url, {
            parameters: params,
            onSuccess: function (transport) {

                var response = $H(eval(transport.responseJSON));
                if (response.get('msg') && response.get('status') == 0)
                {
                    if(response.get('msg'))
                    {
                        alert(response.get('msg'));
                    }
                }
                else
                {
                    if (clue) {
                        alert(clue);
                    }
                    location.href = new_url;
                }
            },
            method: method,
            onFailure: (function (transport) {
                this._failure(transport.responseText);
            }).bind(this)
        });
    }
});
