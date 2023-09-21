<?php
namespace Commerce365\CustomerPrice\Model\ResourceModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Get product type ID by product ID.
 */
class GetProductTypeById
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Retrieve product type by its product ID
     *
     * @param int $productId
     * @return string
     */
    public function execute(int $productId): string
    {
        $connection = $this->resource->getConnection();
        $productTable = $this->resource->getTableName('catalog_product_entity');

        $select = $connection->select()
            ->from(
                $productTable,
                ProductInterface::TYPE_ID
            )->where('entity_id = ?', $productId);

        $result = $connection->fetchOne($select);
        return $result ?: '';
    }
}
