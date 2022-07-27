define([
    'jquery',
    'jquery/ui'
], function($) {
    'use strict';

    var basePriceTextSelector = '.base-price-text';

    return function (swatchRenderer) {

        $.widget('hs_baseprice.swatchRenderer', swatchRenderer, {
            options: {
                basePriceTextSelector: basePriceTextSelector,
                parentProductBasePrice: $(basePriceTextSelector).html()
            },

            _UpdatePrice: function () {
                this._super();
                this._reloadBasePriceText();
            },

            _reloadBasePriceText: function() {
                var $widget = this,
                    $product = $widget.element.parents($widget.options.selectorProduct),
                    $productPrice = $product.find(this.options.selectorProductPrice),
                    options = _.object(_.keys($widget.optionsMap), {}),
                    basePriceText;

                if($widget.options.jsonConfig.isEnabledBasePrice == false) {
                    return;
                }

                $widget.element.find('.' + $widget.options.classes.attributeClass + '[option-selected]').each(function () {
                    var attributeId = $(this).attr('attribute-id');
                    options[attributeId] = $(this).attr('option-selected');
                });

                var basePriceElement = $product.find(basePriceTextSelector);
                basePriceText = $widget.options.jsonConfig.basePriceTexts[_.findKey($widget.options.jsonConfig.index, options)];

                if(basePriceText && basePriceText != undefined) {
                    $(basePriceElement).html(basePriceText);
                } else {
                    $(basePriceElement).html(this.options.parentProductBasePrice);
                }
            }
        });

        return $.hs_baseprice.swatchRenderer;
    };
});
