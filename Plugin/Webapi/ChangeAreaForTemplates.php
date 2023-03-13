<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Webapi;

use Magento\Framework\App\Area;
use Magento\Framework\View\Element\Template\File\Resolver;

class ChangeAreaForTemplates
{
    /**
     * @param Resolver $subject
     * @param string $template
     * @param array $params
     * @return array
     */
    public function beforeGetTemplateFileName(Resolver $subject, $template, $params = []): array
    {
        if (!empty($params['area']) && $params['area'] === Area::AREA_WEBAPI_REST) {
            $params['area'] = Area::AREA_FRONTEND;
        }

        return [$template, $params];
    }
}
