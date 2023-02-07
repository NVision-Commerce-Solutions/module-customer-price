define([
    'uiComponent',
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data',
    'mage/url',
    'Magento_Swatches/js/swatch-renderer',
    'priceBox',
    'configurable'
], function (Component, $, _, customerData, url) {
    'use strict';

    return Component.extend({
        options: {
            priceBoxSelector: '.price-box',
            configurableSelector: '#product_addtocart_form',
            swatchSelector: '[data-role=swatch-options]'
        },
        customerpriceObj: window.customerpriceConfig,

        initialize: function() {
            var payload = [];
            $("[data-role=priceBox]").each(function () {
                payload.push($(this).attr('data-product-id'));
            });
            if (!parseInt(payload.length)) {
                return;
            }

            let customer = customerData.get('customer');
            if (customer().price_token) {
                this.makePriceCall(payload);
            } else {
                customer.subscribe(function () {
                    this.makePriceCall(payload);
                }, this);
            }
        },

        makePriceCall: function(payload) {
            var self = this;
            $.ajax({
                url: url.build( 'rest/V1/customer-price'),
                type: 'POST',
                data: JSON.stringify({
                    productInfo: payload,
                    storeId: this.customerpriceObj.storeId,
                    customerToken: customerData.get('customer')().price_token,
                    productId: this.customerpriceObj.productId
                }),
                cache: true,
                processData: "json",
                contentType: "application/json",
                async: true,
                success: function (data) {
                    try {
                        $.each(data, function (key, productInfo) {
                            $.each(productInfo, function (productId, priceInventory) {
                                try {
                                    $("[data-price-box=product-id-" + productId + "]").each(function () {
                                        if (priceInventory.tierPriceHtml) {
                                            $(this).parent().after(priceInventory.tierPriceHtml);
                                        }
                                        if (priceInventory.priceConfig) {
                                            self.options.priceConfig = JSON.parse(priceInventory.priceConfig);
                                        }
                                        if (priceInventory.configurableConfig) {
                                            self.options.configurableConfig = JSON.parse(priceInventory.configurableConfig);
                                        }
                                        $(this).replaceWith(priceInventory.priceHtml);
                                    });
                                } catch (e) {
                                    console.log(e);
                                }
                            })
                        });

                        if (self.options.priceConfig) {
                            var priceBox = $(self.options.priceBoxSelector);
                            priceBox.priceBox({"priceConfig": self.options.priceConfig});
                        }

                        if (self.options.configurableConfig) {
                            var swatches = $(self.options.swatchSelector);
                            if (swatches.length) {
                                self.reloadSwatches(swatches, self.options.configurableConfig);
                            } else {
                                self.reloadConfigurable(self.options.configurableConfig);
                            }
                        }
                    } catch (e) {
                        console.log(e);
                    }
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });
        },

        reloadSwatches: function (swatches, configurableConfig) {
            var jsonSwatchConfig = swatches.SwatchRenderer('option').jsonSwatchConfig;
            var oldConfigurableConfig = swatches.SwatchRenderer('option').jsonConfig;
            configurableConfig.images = oldConfigurableConfig.images;
            swatches.SwatchRenderer('option').jsonConfig = configurableConfig;
            swatches.SwatchRenderer({"jsonConfig": configurableConfig, "jsonSwatchConfig": jsonSwatchConfig});
        },

        reloadConfigurable: function(configurableConfig) {
            var configurable = $(this.options.configurableSelector);
            var oldSpConfig = configurable.configurable('option');
            configurableConfig.attributes = oldSpConfig.spConfig.attributes;
            configurableConfig.images = oldSpConfig.spConfig.images;
            configurable.configurable({"spConfig": configurableConfig});
        }
    });
});
