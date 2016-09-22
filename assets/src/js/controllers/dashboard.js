module.exports = Backbone.Model.extend({
    initialize: function() {
        "use strict";
        //console.log("It's admin time!");
        this.on_ready();
    },
    on_ready: function(){
        "use strict";
        var $ = jQuery;
        //Do not hide general pricing group on variable products
        $(document.body).on('wc-init-tabbed-panels',() => {
            let $pricing_group = $(".options_group.pricing");
            if($pricing_group.length > 0){
                $pricing_group.addClass("show_if_variable");
            }
        });
        //Show parent price as placeholder in variation prices
        $('#variable_product_options').on('woocommerce_variations_added', (event, count) => {
            //Search for parent product prices, and inject them as placeholders
            let $pricing_group = $(".options_group.pricing"),
                regular_price = $pricing_group.find("input[name=_regular_price]").val(),
                sale_price = $pricing_group.find("input[name=_sale_price]").val();

            $(".variable_pricing").find('input').each((i,el) => {
                if($(el).attr("name").match(/regular_price/)){
                    let my_value = $(el).val();
                    if(my_value == "" && !regular_price == ""){
                        $(el).attr("placeholder",regular_price);
                    }
                }else if($(el).attr("name").match(/sale_price/)){
                    let my_value = $(el).val();
                    if(my_value == "" && !sale_price == ""){
                        $(el).attr("placeholder",sale_price);
                    }
                }
            });
        });
    }
});