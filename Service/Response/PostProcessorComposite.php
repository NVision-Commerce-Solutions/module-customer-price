<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Response;

use Magento\Framework\Exception\LocalizedException;

class PostProcessorComposite implements PostProcessorInterface
{
    public function __construct(private readonly array $postProcessors) {}

    public function process(array $data, $productId): array
    {
        foreach($this->postProcessors as $postProcessor) {
            if (!$postProcessor instanceof  PostProcessorInterface) {
                throw new LocalizedException(
                    __("Provider %1 should implements PostProcessorInterface", get_class($postProcessor))
                );
            }

            $data = $postProcessor->process($data, $productId);
        }


        return $data;
    }
}
