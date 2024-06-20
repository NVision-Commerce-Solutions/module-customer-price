<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model;

use Commerce365\CustomerPrice\Service\CurrentStore;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_HIDE_PRICES = 'commerce365config_general/b2b_pricing/hide_prices_guest';
    public const XML_PATH_AJAX_ENABLED = 'commerce365config_general/b2b_pricing/ajax_enabled';
    private const XML_PATH_CACHE_HOURS = 'commerce365config_general/b2b_pricing/cache_hours';
    public const XML_PATH_SPECIAL_PRICE = 'commerce365config_general/b2b_pricing/use_special_price';
    private const XML_PATH_CACHE_ENABLE = 'commerce365config_general/b2b_pricing/db_caching_enabled';
    private const XML_PATH_HIGH_LEVEL_CACHE_ENABLE = 'commerce365config_general/b2b_pricing/high_level_cache_enabled';
    private const XML_PATH_SHOW_UOM = 'commerce365config_general/b2b_pricing/show_priceperuom';
    private const XML_PATH_SHOW_UOM_TIER = 'commerce365config_general/b2b_pricing/show_priceperuom_tier';
    private const XML_PATH_USE_MINIMAL_QTY = 'commerce365config_general/b2b_pricing/use_minimal_qty';
    private const XML_PATH_CONFIGURABLE_PRESELECT = 'commerce365config_general/b2b_pricing/configurable_preselect';


    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly CurrentStore $currentStore
    ) {}

    /**
     * @return bool
     */
    public function isAjaxEnabled(): bool
    {
        $storeId = $this->currentStore->getId();

        return $this->scopeConfig->isSetFlag(self::XML_PATH_AJAX_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return bool
     */
    public function isCachingEnabled(): bool
    {
        $storeId = $this->currentStore->getId();

        return $this->scopeConfig->isSetFlag(self::XML_PATH_CACHE_ENABLE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return bool
     */
    public function isHidePricesGuest(): bool
    {
        $storeId = $this->currentStore->getId();

        return $this->scopeConfig->isSetFlag(self::XML_PATH_HIDE_PRICES, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return bool
     */
    public function useSpecialPrice(): bool
    {
        $storeId = $this->currentStore->getId();

        return $this->scopeConfig->isSetFlag(self::XML_PATH_SPECIAL_PRICE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getCacheHours()
    {
        $storeId = $this->currentStore->getId();

        return $this->scopeConfig->getValue(self::XML_PATH_CACHE_HOURS, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function showPricePerUom(): bool
    {
        $storeId = $this->currentStore->getId();

        return $this->scopeConfig->isSetFlag(self::XML_PATH_SHOW_UOM, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function useMinSalableQty(): bool
    {
        $storeId = $this->currentStore->getId();

        return $this->scopeConfig->isSetFlag(self::XML_PATH_USE_MINIMAL_QTY, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function showPricePerUomTier(): bool
    {
        $storeId = $this->currentStore->getId();

        return $this->scopeConfig->isSetFlag(self::XML_PATH_SHOW_UOM_TIER, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function preselectConfigurable(): bool
    {
        $storeId = $this->currentStore->getId();

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIGURABLE_PRESELECT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return bool
     */
    public function isHighLevelCachingEnabled(): bool
    {
        $storeId = $this->currentStore->getId();

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HIGH_LEVEL_CACHE_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
