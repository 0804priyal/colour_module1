define([
    'jquery',
    'jquery/ui',
    'Magento_ConfigurableProduct/js/configurable'
], function($){

    var basePriceTextSelector = '.base-price-text';

    $.widget('hs_baseprice.configurable', $.mage.configurable, {
        options: {
            basePriceTextSelector: basePriceTextSelector
        },

        _initializeOptions: function () {
            this._super();

            this.options.parentProductBasePrice = $('div[data-product-id="'+this.options.spConfig.productId+'"][data-role="priceBox"] ' + basePriceTextSelector).html()
        },

        _reloadPrice: function () {
            this._super();
            this._reloadBasePriceText();
        },

        _reloadBasePriceText: function() {
            if(this.options.spConfig.isEnabledBasePrice == false) {
                return;
            }

            var basePriceTexts = {},
                elements = _.toArray(this.options.settings),
                hasBasePriceText = false;

            _.each(elements, function (element) {
                var selected = element.options[element.selectedIndex],
                    config = selected && selected.config,
                    basePriceText;

                if (config && config.allowedProducts.length === 1 && !hasBasePriceText) {
                    basePriceText = this.options.spConfig.basePriceTexts[_.first(config.allowedProducts)];
                    hasBasePriceText = true;
                }

                var priceElement = $('div[data-product-id="'+this.options.spConfig.productId+'"][data-role="priceBox"]');
                if(basePriceText && basePriceText !== undefined) {
                    $(priceElement).parent().find(this.options.basePriceTextSelector).html(basePriceText);
                } else {
                    $(priceElement).parent().find(this.options.basePriceTextSelector).html(this.options.parentProductBasePrice);
                }
            }, this);
        },
    });

    return $.hs_baseprice.configurable;
});
