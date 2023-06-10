<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Locale\ListsInterface;

class Currency extends AbstractSource implements OptionSourceInterface
{
    private ListsInterface $lists;

    public function __construct(ListsInterface $lists)
    {
        $this->lists = $lists;
    }

    public function getAllOptions()
    {
        $options = $this->lists->getOptionCurrencies();
        array_unshift(
            $options,
            ['value' => '', 'label' => 'Default']
        );

        return $options;
    }
}
