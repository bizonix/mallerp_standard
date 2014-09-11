
function toggle_tree_update_content(current, content_url, child_tree_url, id, level, plus_or_minus)
{
    var parent = $(current.parentNode);
    var ul = parent.next();
    if ( ! plus_or_minus)
    {
        plus_or_minus = $(current).previous(1);
    }
    if (ul.childElements().size())
    {
        parent.toggleClassName('x-tree-node-collapsed');
        parent.toggleClassName('x-tree-node-expanded');
        plus_or_minus.toggleClassName('x-tree-elbow-plus');
        plus_or_minus.toggleClassName('x-tree-elbow-minus');
    }
    else
    {
        // fetch child tree
        var params = {'id': id, 'level': level};
        parent.toggleClassName('x-tree-node-loading');
        new Ajax.Request(child_tree_url, {
            parameters: params,
            onSuccess: function (transport) {
                if ( ! helper.check_session(transport))
                {
                    return;
                }
                var response = transport.responseText;
                ul.update(response)
                parent.toggleClassName('x-tree-node-loading');
                parent.toggleClassName('x-tree-node-collapsed');
                parent.toggleClassName('x-tree-node-expanded');
                plus_or_minus.toggleClassName('x-tree-elbow-plus');
                plus_or_minus.toggleClassName('x-tree-elbow-minus');
                current.addClassName('x-tree-node-expanded');

            },
            onFailure: function (transport) {
            }
        });
    }

    update_content(current, content_url, id, true);
    
    helper.show_loading();
}

function toggle_tree(current, child_tree_url, id, level, plus_or_minus)
{
    var parent = $(current.parentNode);
    var ul = parent.next();
    if ( ! plus_or_minus)
    {
        plus_or_minus = $(current).previous(1);
    }
    if (ul.childElements().size())
    {
        ul.toggle();
        parent.toggleClassName('x-tree-node-collapsed');
        parent.toggleClassName('x-tree-node-expanded');
        plus_or_minus.toggleClassName('x-tree-elbow-plus');
        plus_or_minus.toggleClassName('x-tree-elbow-minus');
    }
    else
    {
        // fetch child tree
        var params = {'id': id, 'level': level};
        parent.toggleClassName('x-tree-node-loading');
        new Ajax.Request(child_tree_url, {
            parameters: params,
            onSuccess: function (transport) {
                if ( ! helper.check_session(transport))
                {
                    return;
                }
                var response = transport.responseText;
                ul.update(response)
                ul.show();
                parent.toggleClassName('x-tree-node-loading');
                parent.toggleClassName('x-tree-node-collapsed');
                parent.toggleClassName('x-tree-node-expanded');
                plus_or_minus.toggleClassName('x-tree-elbow-plus');
                plus_or_minus.toggleClassName('x-tree-elbow-minus');
                current.addClassName('x-tree-node-expanded');

            },
            onFailure: function (transport) {
            }
        });
    }
}

function update_content(current, content_url, id, no_plus_or_minus)
{
    var parent = $(current.parentNode);
    var ul = parent.next();
    var plus_or_minus = $(current).previous(1);
    var all_leaf = $$('div[class~="x-tree-node-leaf-mark"]');
    var main_contents = $$('div[class="main-col"]');
    params = {'id': id};
    new Ajax.Request(content_url, {
        parameters: params,
        onSuccess: function (transport) {
            if ( ! helper.check_session(transport))
            {
                return;
            }
            all_leaf.each(function (el) {
                el.addClassName('x-tree-node-collapsed');
                el.removeClassName('x-tree-node-expanded');
            });
            parent.removeClassName('x-tree-node-collapsed');
            parent.addClassName('x-tree-node-expanded');
            if ( ! no_plus_or_minus)
            {
                plus_or_minus.toggleClassName('x-tree-elbow-plus');
                plus_or_minus.toggleClassName('x-tree-elbow-minus');
            }
            var response = transport.responseText;
            $('main-content').update(response);
            main_contents.each(function (el) {
                el.hide();
            });
            $('main-content').show();
            ul.toggle();
            helper.hide_loading();
        },
        onFailure: function (transport) {
            helper.hide_loading();
        }
    });
    helper.show_loading();
}

function collapse_tree()
{
    var all_cts = $$('ul[class~="x-tree-node-ct"]');
    var all_minus = $$('img[class~="x-tree-elbow-minus"]');
    all_cts.each(function (el) {
        el.hide();
    });
    all_minus.each(function(el) {
        el.toggleClassName('x-tree-elbow-plus');
        el.toggleClassName('x-tree-elbow-minus');
    });
}

function expand_tree()
{
    var all_cts = $$('ul[class~="x-tree-node-ct"]');
    all_cts.each(function (el) {
        if (el.childElements().size())
        {
            var previous_children = el.previous(0).childElements();
            previous_children.each(function (c) {
                if (c.hasClassName('x-tree-ec-icon'))
                {
                    c.removeClassName('x-tree-elbow-plus');
                    c.addClassName('x-tree-elbow-minus');
                    throw $break;
                }
            });

            el.show();
        }
    });

}