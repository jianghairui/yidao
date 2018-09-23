!(function (doc, win) {
    var el = doc.documentElement;
    //resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize';

    function setSize() {
        var w = el.clientWidth;
        if (!w) return;
        w=w>480?480:w;
        w=w<320?320:w;
        el.style.fontSize = (100 * (w / 750)).toFixed(3) + 'px';
        //1rem相当于1080的图中的100px，最小320px,最大480px，超过这个尺寸后rem与px的换算比例不再变化

    }
    if (!doc.addEventListener) return;
    setSize();
    win.addEventListener('resize', setSize, false);
    win.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            setSize();
        }
    }, false);
})(document, window);