module.exports = Backbone.Model.extend({
    initialize: function() {
        "use strict";
        //console.log("It's admin time!");
        this.on_ready();
    },
    on_ready: function(){
        "use strict";
        var $ = jQuery;
        $(document.body).on('wc-init-tabbed-panels',() => {
            let $pricing_group = $(".options_group.pricing");
            if($pricing_group.length > 0){
                $pricing_group.addClass("show_if_variable");
            }
        })
    }
});