//= require js/app.linkify
//= require js/app.scrollTo

this.app = this.app || {};

(function(window, $, app){
    var $body = $('body');
    
    if(window.location.hash){
        var $scrollEl = $(window.location.hash.replace(/([^a-zA-Z0-9-_#])/g, '\\$1'));
        if($scrollEl.length){
            setTimeout(function(){
                var pos = $scrollEl.offset().top - 80 - 32;
                
                window.scrollTo(0, pos);
            }, 1);
        }
    }
    
    app.linkifyAnchors([2, 3, 4, 5, 6], '#content');
    
    $body.on('click', '[data-scrollto], [href^="#"]', function(e){
        e.preventDefault();
        
        var $this = $(this),
            location = $this.data('scrollto') || $this.attr('href').replace('#', '');
        
        app.scrollTo(location);
    });
})(window, $, this.app)
