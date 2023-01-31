<?php
namespace Aheadworks\FaqFree\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class ProductArticleRepository
{
    public const PRODUCT_ARTICLE_TABLE_NAME = 'aw_faq_product_article';
    public const ID_COLUMN = 'id';
    public const PRODUCT_ID_COLUMN = 'product_id';
    public const ARTICLE_ID_COLUMN = 'article_id';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * ProductArticleRepository constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get product articles
     *
     * @param ProductInterface $product
     * @return int[]
     */
    public function get($product)
    {
        $connection = $this->getConnection();
        $productArticlesSelect = $connection->select()
            ->from($this->getTableName())
            ->where(self::PRODUCT_ID_COLUMN . ' = :product_id');

        $productArticlesData = $connection->fetchAll(
            $productArticlesSelect,
            [':product_id' => $product->getId()]
        );

        return $this->prepareProductArticlesIds($productArticlesData);
    }

    /**
     * Save product articles
     *
     * @param ProductInterface $product
     * @param int[] $articlesIds
     * @return bool
     */
    public function save($product, $articlesIds)
    {
        $productId = $product->getId();
        $productArticlesData = [];

        foreach ($articlesIds as $articleId) {
            $productArticlesData[] = [
                self::PRODUCT_ID_COLUMN => $productId,
                self::ARTICLE_ID_COLUMN => $articleId,
            ];
        }

        if (!empty($productArticlesData)) {
            $this->getConnection()->insertMultiple($this->getTableName(), $productArticlesData);
        }

        return true;
    }

    /**
     * Delete product articles
     *
     * @param ProductInterface $product
     * @return bool
     */
    public function delete($product)
    {
        $productId = $product->getId();
        $this->getConnection()
            ->delete($this->getTableName(), [self::PRODUCT_ID_COLUMN . ' = ?' => $productId]);

        return true;
    }

    /**
     * Retrieve table name
     *
     * @return string
     */
    private function getTableName()
    {
        return $this->resourceConnection->getTableName(self::PRODUCT_ARTICLE_TABLE_NAME);
    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     */
    private function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }

    /**
     * Prepare product articles ids
     *
     * @param array $productsArticlesData
     * @return int[]
     */
    private function prepareProductArticlesIds($productsArticlesData)
    {
        $productArticlesIds = [];

        foreach ($productsArticlesData as $item) {
            if (!empty($item[self::ARTICLE_ID_COLUMN])) {
                $productArticlesIds[] = $item[self::ARTICLE_ID_COLUMN];
            }
        }

        return $productArticlesIds;
    }
}
