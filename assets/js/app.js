(function(window, $){
    var linkifyAnchors = function(level, container){
        if (!(level instanceof window.Array))
            level = [level];
        
        $.each(level, function(i, value){
            console.log([i, value]);
            
            if(level < 1 || level > 6)
                return;
            
            $(container + ' h' + level + '[id]').each(function(){
                var $this = $(this),
                    h = $this.html(),
                    id = $this.attr('id'),
                    $a;
                
                $a = $('<a>').attr('href', window.location.pathname + '#' + id).html(h);
                $this.html($a);
            });
        });
    };
    
    linkifyAnchors([2, 3, 4, 5, 6], '#content');
})(window, $)
