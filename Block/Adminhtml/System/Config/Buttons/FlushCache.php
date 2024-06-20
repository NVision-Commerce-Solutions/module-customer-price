<?php

namespace Commerce365\CustomerPrice\Block\Adminhtml\System\Config\Buttons;

use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class FlushCache extends Field
{
    protected $_template = 'Commerce365_CustomerPrice::system/config/buttons/flushcache.phtml';

    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    public function getAjaxUrl(): string
    {
        return $this->getUrl('commerce365_customerprice/cache/flush');
    }

    public function getButtonHtml(): string
    {
        $data = [
            'id'    => 'flushcache_button',
            'label' => __('Flush Customer Price Cache'),
        ];

        /** @var Button $button */
        $button = $this->getLayout()->createBlock(Button::class)->setData($data);
        $button->setDataAttribute(
            [
                'mage-init' => '{"Commerce365_CustomerPrice/js/system/config/buttons/flushcache": {
                            "submitUrl":"' . $this->getAjaxUrl() . '"
                        }
                    }',
            ]
        );

        return $button->toHtml();
    }
}
