<?php

namespace Commerce365\CustomerPrice\Service\Response;

interface PostProcessorInterface
{
    public function process(array $data, $productId): array;
}
