(function(window) {
        //获取页面宽度
        var winW = document.documentElement.clientWidth || document.body.clientWidth;
        //根据页面宽度设置html字体大小
        document.documentElement.style.fontSize = winW / 7.5 + "px";
        //当页面大小发生改变,重新修正rem为新窗口尺寸
        window.onresize = function() {
            document.documentElement.style.fontSize = (document.documentElement.clientWidth || document.body.clientWidth) / 7.5 + "px";
        }
    })(window);