<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Response;

use Commerce365\CustomerPrice\Service\Product\TaxClassReader;
use Magento\Catalog\Helper\Data;
use Magento\Tax\Model\Config;

class TaxPostProcessor implements PostProcessorInterface
{
    public function __construct(
        private readonly Data $catalogHelper,
        private readonly Config $taxConfig,
        private readonly TaxClassReader $taxClassReader
    ) {}

    public function process(array $data, $productId): array
    {
        $priceDisplayType = $this->taxConfig->getPriceDisplayType();
        if ($priceDisplayType !== Config::DISPLAY_TYPE_INCLUDING_TAX) {
            return $data;
        }

        $pseudoProduct = new \Magento\Framework\DataObject();
        $taxClassId = $this->taxClassReader->getTaxClassId((int) $productId);
        $pseudoProduct->setTaxClassId($taxClassId);

        $data['price'] = $this->catalogHelper->getTaxPrice($pseudoProduct, $data['price']);
        $data = $this->processPricePerUOM($data, $pseudoProduct);

        if (!empty($data['tier_prices'])) {
            foreach ($data['tier_prices'] as &$tierPrice) {
                $tierPrice['price'] = $this->catalogHelper->getTaxPrice($pseudoProduct, $tierPrice['price']);
                $tierPrice = $this->processPricePerUOM($tierPrice, $pseudoProduct);
            }
        }

        return $data;
    }

    private function processPricePerUOM($data, $product)
    {
        if (isset($data['additional']['pricePerUOM'])) {
            $data['additional']['pricePerUOM'] = $this->catalogHelper->getTaxPrice(
                $product,
                $data['additional']['pricePerUOM']
            );
        }

        return $data;
    }
}
