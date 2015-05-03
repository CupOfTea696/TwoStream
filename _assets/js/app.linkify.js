this.app = this.app || {};
(function(window, $, app){
    var linkifyAnchors = function(level, container){
        if (!(level instanceof window.Array))
            level = [level];
        
        $.each(level, function(i, level){
            if(level < 1 || level > 6)
                return;
            
            $(container + ' h' + level + '[id]').each(function(){
                var $this = $(this),
                    h = $this.html(),
                    id = $this.attr('id'),
                    $a;
                
                $a = $('<a>').attr('data-scrollto', id).html(h);
                $this.html($a);
            });
        });
    };
    
    app.linkifyAnchors = linkifyAnchors;
})(window, $, this.app)
