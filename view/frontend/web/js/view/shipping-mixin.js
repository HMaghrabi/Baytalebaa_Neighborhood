define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (shippingComponent) {
        return wrapper.wrap(shippingComponent.prototype, 'validateShippingInformation', function (originalMethod) {
            var result = originalMethod();
            if (result) {
                var shippingAddress = this.source.get('shippingAddress');
                var neighborhood = shippingAddress.neighborhood;

                if (neighborhood) {
                    if (!this.source.get('shippingAddress.extension_attributes')) {
                        this.source.set('shippingAddress.extension_attributes', {});
                    }

                    this.source.set('shippingAddress.extension_attributes.neighborhood', neighborhood);
                }
            }
            return result;
        });
    };
});