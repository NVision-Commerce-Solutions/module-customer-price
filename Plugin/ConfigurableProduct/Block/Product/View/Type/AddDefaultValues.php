<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\ConfigurableProduct\Block\Product\View\Type;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\Additional\PricePerUom\GetPricePerUom;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as Subject;
use Magento\Framework\Serialize\Serializer\Json;

class AddDefaultValues
{
    private Json $jsonSerializer;
    private Config $config;

    /**
     * @param Json $jsonSerializer
     * @param Config $config
     */
    public function __construct(Json $jsonSerializer, Config $config)
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->config = $config;
    }

    /**
     * @param Subject $configurable
     * @param string $result
     * @return string
     */
    public function afterGetJsonConfig(Subject $configurable, string $result): string
    {
        $jsonConfig = $this->jsonSerializer->unserialize($result);

        if ($this->config->preselectConfigurable()) {
            $jsonConfig = $this->addDefaultValues($jsonConfig);
        }

        return $this->getResult($jsonConfig);
    }

    private function getResult(array $jsonConfig)
    {
        return $this->jsonSerializer->serialize($jsonConfig);
    }

    private function addDefaultValues($jsonConfig)
    {
        $jsonConfig['defaultValues'] = array_first($jsonConfig['index']);

        return $jsonConfig;
    }
}
