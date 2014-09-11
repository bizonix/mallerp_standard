
function get_track_number(url) {
    helper.show_loading();
    return helper.ajax_reflesh(url, {});
}

function update_unconfirmed_count(url) {
    var e = new Ajax.PeriodicalUpdater(
        'unconfirmed_count',
        url,
        {
            onComplete: function () {
                if ($('unconfirmed_count').innerHTML.strip() == '0') {
                    e.stop();
                    location.reload();
                }
            }
        }
    );
}

function stop_get_track_number(url) {
    return helper.ajax_reflesh(url, {});
}