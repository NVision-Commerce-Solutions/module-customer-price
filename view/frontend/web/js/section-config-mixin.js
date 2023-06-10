define([
    'mage/utils/wrapper'
], function (wrapper) {
    'use strict';

    return function (sectionConfig) {
        sectionConfig.getAffectedSections = wrapper.wrapSuper(sectionConfig.getAffectedSections, function (url) {
            if (typeof url === 'string' && url.indexOf('/customer-price') !== -1) {
                return [];
            }
            return this._super(url);
        });

        return sectionConfig;
    };
});
