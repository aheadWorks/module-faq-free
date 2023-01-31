<?php

namespace Aheadworks\FaqFree\Model\Article;

use Aheadworks\FaqFree\Model\ResourceModel\Article\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\FaqFree\Model\ResourceModel\Article\Collection;
use Aheadworks\FaqFree\Model\Article;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;

/**
 * FAQ Article DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    private $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $articleCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $articleCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $articleCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();

        /**
         * @var Article|ArticleInterface $article
         */
        foreach ($items as $article) {
            $loadedData = $article->getData();
            $this->loadedData[$article->getArticleId()] = $loadedData;
        }

        $data = $this->dataPersistor->get('faq_article');
        if (!empty($data)) {
            $article = $this->collection->getNewEmptyItem();
            $article->setData($data);
            $this->loadedData[$article->getArticleId()] = $article->getData();
            $this->dataPersistor->clear('faq_article');
        }

        return $this->loadedData;
    }
}
