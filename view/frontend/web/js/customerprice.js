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
            this.createMutationObserver();
            this.run();
        },

        run: function(container = null) {
            var payload = [];
            container = container ?? document;
            container.querySelectorAll("[data-role=priceBox]").forEach(element => {
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
                            this.processTierPrices(productInfo, element)
                        }
                        element.outerHTML = productInfo.priceHtml;
                        if (productInfo.type !== 'grouped_child') {
                            self.processPriceConfig(productInfo);
                        }

                        self.processConfigurable(productInfo.configurableConfig, productInfo.productId);
                    });
                } catch (e) {
                    console.log(e);
                }
            });
        },

        processTierPrices: function (productInfo, element) {
            if (productInfo.type === 'grouped_child') {
                const tierPriceElementTr = document.createElement('tr');
                tierPriceElementTr.classList.add('row-tier-price');
                const tierPriceElementTd = document.createElement('td');
                tierPriceElementTd.setAttribute('colspan', 2);
                tierPriceElementTd.innerHTML = productInfo.tierPriceHtml;
                tierPriceElementTr.appendChild(tierPriceElementTd);
                element.parentNode.parentNode.after(tierPriceElementTr)
            } else {
                const tierPrice = this.getElementFromHtml(productInfo.tierPriceHtml);
                element.parentNode.after(tierPrice);
            }
        },

        processPriceConfig: function (productInfo) {
            if (!productInfo.priceConfig) {
                return;
            }
            const config = JSON.parse(productInfo.priceConfig);
            const priceBox = $('[data-price-box=product-id-' + productInfo.productId + '].price-final_price');
            priceBox.priceBox({"priceConfig": config});

            if (this.customerpriceObj.productId && this.customerpriceObj.productId === productInfo.productId) {
                if (productInfo.tierPriceHtml && productInfo.type !== 'configurable') priceBox.priceBox('updateProductTierPrice');
            }
        },

        processConfigurable: function (config, productId) {
            if (!config) {
                return;
            }
            config = JSON.parse(config);
            if (this.customerpriceObj.productId && this.customerpriceObj.productId === productId) {
                var swatches = $(this.options.swatchSelector);
                if (swatches.length) {
                    this.reloadSwatches(swatches, config);
                } else {
                    this.reloadConfigurable(config);
                }
            } else {
                var swatches = $('[data-role=swatch-option-' + productId + ']');
                if (swatches.length) {
                    this.reloadSwatches(swatches, config);
                }
            }
            $('#qty').off('input');
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
            const configurable = $(this.options.configurableSelector);
            let oldSpConfig = configurable.configurable('option');
            configurableConfig.attributes = oldSpConfig.spConfig.attributes;
            configurableConfig.images = oldSpConfig.spConfig.images;
            const tierPriceTemplate = $(oldSpConfig.tierPriceTemplateSelector).html();
            configurable.configurable({"spConfig": configurableConfig});
            configurable.configurable({"tierPriceTemplate": tierPriceTemplate});
        },

        createMutationObserver: function() {
            const self = this;
            var mutationObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function (addedNode) {
                        if (addedNode instanceof Node && 'querySelectorAll' in addedNode
                            && addedNode.classList.value !== 'price-box price-final_price') {
                            self.run(addedNode);
                        }
                    });
                });
            });
            const productsContainer = document.querySelector('div.column.main');
            mutationObserver.observe(productsContainer, {
                attributes: true,
                characterData: true,
                childList: true,
                subtree: true,
                attributeOldValue: true,
                characterDataOldValue: true
            });
        }
    });
});
