function accordion(el) {
    var elup;
    var eldown = $(el.parentNode.id+'-body');
    
    if (eldown.hasClassName('visible'))
    {
        Effect.SlideUp(eldown, {
            duration: 0.5
        });
        eldown.removeClassName('visible');      
    }
    else
    {
        eldown.addClassName('visible');
        new Effect.SlideDown(eldown, {
            duration: 0.5
        });
    }
}

//pass in ID of container element that has all instances of apanels
function accordion_init(id) {
    var apanels = document.getElementsByClassName('panel_body',id);
    for (var i=0;i<apanels.length;i++){
        apanels[i].style.display = 'none';
    }
    var velems = document.getElementsByClassName('visible');
    for (var i=0;i<velems.length;i++){
        $(velems[i]).style.display = 'block';
    }
}

function accordion_init_all() {
    var apanels = document.getElementsByClassName('panel_body');
    for (var i=0;i<apanels.length;i++){
        apanels[i].style.display = 'none';
    }
    var velems = document.getElementsByClassName('visible');
    for (var i=0;i<velems.length;i++){
        $(velems[i]).style.display = 'block';
    }
}
//The following line was added as I couldn't get the addEvent() lines to work from Brian's Code sample on the scriptaculous wiki. This approach works in both Firefox 1.5 and IE 6.
document.observe('dom:loaded', function (){
    accordion_init_all();
});
