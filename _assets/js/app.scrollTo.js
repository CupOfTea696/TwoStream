this.app = this.app || {};

(function(window, $, app){
    var scrollTo = function(location){
        var pos;
        window.history.pushState({}, window.document.title, window.location.pathname + '#' + location);
        
        pos = $('#' + location.replace(/([^a-zA-Z0-9-_])/g, '\\$1')).offset().top - 96;
        pos = Math.ceil(pos);
        
        var distance = window.Math.abs($(window).scrollTop() - pos);
        var speed = 800 / 1000; // in px/ms
        var duration = distance / speed;
        $('body').animate({scrollTop: pos}, duration);
    }
    
    app.scrollTo = scrollTo;
})(window, $, this.app);
