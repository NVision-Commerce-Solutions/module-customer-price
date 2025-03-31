# module-customer-price

## Commerce 365 for Magento - Magento 2 Extension - Customer Price Module

For more information about Commerce 365 for Magento, see: [Commerce 365 for Magento](https://n.vision/products/commerce-365-for-magento/).

## Customization

### Translations

When customizing translations, ensure that they are added to the module's `i18n` folder rather than the theme. This is due to an issue in Magento where translations placed in themes may not be applied correctly. More details can be found in the related Magento issue: [Magento 2 Issue #26333](https://github.com/magento/magento2/issues/26333).

To add translations, place your language-specific CSV files in:

```
app/code/Vendor/Module/i18n/
```

For example, for German translations, add:

```
app/code/Vendor/Module/i18n/de_DE.csv
```

This ensures that translations are properly loaded and applied throughout the module.

