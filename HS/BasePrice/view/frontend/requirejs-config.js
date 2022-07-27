var config = {
    map: {
        '*': {
            configurable: 'HS_BasePrice/js/configurable'
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'HS_BasePrice/js/swatch-renderer': true
            }
        }
    }
};
