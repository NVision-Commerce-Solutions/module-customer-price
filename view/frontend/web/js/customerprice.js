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
            document.querySelectorAll("[data-role=priceBox]").forEach(element => {
                payload.push(element.getAttribute('data-product-id'));
            });
            if (!parseInt(payload.length)) {
                return;
            }

            let customer = customerData.get('customer');
            if (customer().price_token) {
                this.makePriceCall(payload).catch(error => {error.message});
            } else {
                customer.subscribe(function () {
                    this.makePriceCall(payload).catch(error => {error.message});
                }, this);
            }
        },

        makePriceCall: async function (payload) {
            var self = this;
            const response = await fetch(url.build('rest/V1/customer-price'), {
                method: 'POST',
                body: JSON.stringify({
                    productInfo: payload,
                    storeId: this.customerpriceObj.storeId,
                    customerToken: customerData.get('customer')().price_token,
                    productId: this.customerpriceObj.productId
                }),
                headers: {
                    'Content-Type': 'application/json',
                    "X-Requested-With": "XMLHttpRequest",
                }
            });
            if (!response.ok) {
                const message = `An error has occured: ${response.status}`;
                throw new Error(message);
            }
            const responseData = await response.json();
            responseData.forEach(productInfo => {
                try {
                    document.querySelectorAll("[data-price-box=product-id-" + productInfo.productId + "]").forEach(element => {
                        if (productInfo.tierPriceHtml) {
                            const tierPrice = this.getElementFromHtml(productInfo.tierPriceHtml);
                            element.parentNode.after(tierPrice);
                        }
                        if (productInfo.priceConfig) {
                            self.options.priceConfig = JSON.parse(productInfo.priceConfig);
                        }
                        if (productInfo.configurableConfig) {
                            self.options.configurableConfig = JSON.parse(productInfo.configurableConfig);
                        }
                        element.outerHTML = productInfo.priceHtml;
                    });
                } catch (e) {
                    console.log(e);
                }
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
        },

        getElementFromHtml: function(html) {
            const placeholder = document.createElement('div');
            placeholder.innerHTML = html;

            return placeholder.firstElementChild;
        },

        reloadSwatches: function (swatches, configurableConfig) {
            var jsonSwatchConfig = swatches.SwatchRenderer('option').jsonSwatchConfig;
            var oldConfigurableConfig = swatches.SwatchRenderer('option').jsonConfig;
            configurableConfig.mappedAttributes = oldConfigurableConfig.mappedAttributes;
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
