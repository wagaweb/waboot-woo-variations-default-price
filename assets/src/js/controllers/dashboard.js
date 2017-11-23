module.exports = Backbone.Model.extend({
    initialize: function() {
        "use strict";
        var $ = jQuery;
        //console.log("It's admin time!");
        this.on_ready();
        $(window).on("load",() => {
            this.on_load();
        })
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

        /**
         * Search for parent product prices, and inject them as placeholders. Used below.
         */
        let set_placeholders = function(){
            let $pricing_group = $(".options_group.pricing"),
                regular_price = $pricing_group.find("input[name=_regular_price]").val(),
                sale_price = $pricing_group.find("input[name=_sale_price]").val(),
                sale_price_from = $pricing_group.find("input[name=_sale_price_dates_from]").val(),
                sale_price_to = $pricing_group.find("input[name=_sale_price_dates_to]").val();

            $(".variable_pricing").find('input').each((i,el) => {
                if($(el).attr("name").match(/regular_price/)){
                    if($(el).val() === "" && regular_price !== ""){
                        $(el).attr("placeholder",regular_price);
                    }
                }else if($(el).attr("name").match(/sale_price_dates_from/)){
                    if($(el).val() === "" && sale_price_from !== ""){
                        $(el).attr("placeholder",sale_price_from);
                    }
                }else if($(el).attr("name").match(/sale_price_dates_to/)){
                    if($(el).val() === "" && sale_price_to !== ""){
                        $(el).attr("placeholder",sale_price_to);
                    }
                }else if($(el).attr("name").match(/sale_price/)){
                    if($(el).val() === "" && sale_price !== ""){
                        $(el).attr("placeholder",sale_price);
                    }
                }
            });
        };

        //Show parent price as placeholder in variation prices
        $('#variable_product_options').on('woocommerce_variations_added', (event, count) => {
            set_placeholders();
        });

        $( '#woocommerce-product-data' ).on('woocommerce_variations_loaded', (event) => {
            set_placeholders();
        });
    },
    on_load: function(){
        "use strict";
        var $ = jQuery;
        //Show price and sale price in quick edit for product variable
        $( '#the-list' ).on( 'click', '.editinline', function() {
            let post_id = $( this ).closest( 'tr' ).attr( 'id' );
            post_id = post_id.replace( 'post-', '' );
            let $wc_inline_data = $( '#woocommerce_inline_' + post_id );
            // Conditional display
            let product_type       = $wc_inline_data.find( '.product_type' ).text(),
                product_is_virtual = $wc_inline_data.find( '.product_is_virtual' ).text();

            if ( 'variable' === product_type ) {
                $( '.price_fields', '.inline-edit-row' ).show().removeAttr( 'style' );
            }
        });
    }
});