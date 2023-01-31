<?php
namespace Aheadworks\FaqFree\Model\Category;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\ResourceModel\Category\CollectionFactory;
use Aheadworks\FaqFree\Model\ResourceModel\Category\Collection;
use Aheadworks\FaqFree\Model\Url;
use Aheadworks\FaqFree\Model\Category;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\FaqFree\Api\Data\CategoryExtensionInterface;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var Url
     */
    private $url;
    
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
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $categoryCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Url $url
     * @param FileInfo $fileInfo
     * @param RequestInterface $request
     * @param DataObjectProcessor $dataObjectProcessor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $categoryCollectionFactory,
        DataPersistorInterface $dataPersistor,
        Url $url,
        FileInfo $fileInfo,
        RequestInterface $request,
        DataObjectProcessor $dataObjectProcessor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $categoryCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->meta = $this->prepareMeta($this->meta);
        $this->url = $url;
        $this->fileInfo = $fileInfo;
        $this->request = $request;
        $this->dataObjectProcessor = $dataObjectProcessor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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

        /** @var CategoryInterface|Category $category */
        foreach ($items as $category) {
            $this->loadedData[$category->getCategoryId()] = $this->prepareFormDataForCategory($category);
        }

        $id = $this->request->getParam($this->getRequestFieldName());
        $data = $this->dataPersistor->get('faq_category');
        if (!empty($data)) {
            $category = $this->collection->getNewEmptyItem();
            $category->setData($data);
            $this->loadedData[$category->getCategoryId()] = $category->getData();
            $this->dataPersistor->clear('faq_category');
        } else {
            $parentId = $this->request->getParam('parent', 0);
            /** @var CategoryInterface|Category $category */
            foreach ($items as $category) {
                if ($id == $category->getId() && $parentId) {
                    $category->setParentId($parentId);
                    $this->loadedData[$category->getCategoryId()] = $category->getData();
                }
            }
            if ($parentId = $this->request->getParam('parent', 0)) {
                $this->loadedData[$id][CategoryInterface::PARENT_ID] = $parentId;
            }
        }

        return $this->loadedData;
    }

    /**
     * Prepare data for category
     *
     * @param Category $category
     * @return mixed
     */
    private function prepareFormDataForCategory($category)
    {
        $data = $category->getData();
        if (isset($data[CategoryInterface::CATEGORY_ICON])) {
            $imageName = $category->getCategoryIcon();
            unset($data[CategoryInterface::CATEGORY_ICON]);
            if ($this->fileInfo->isExist($imageName)) {
                $stat = $this->fileInfo->getStat($imageName);
                $data[CategoryInterface::CATEGORY_ICON] = [
                    [
                        'name' => $imageName,
                        'url' => $this->url->getCategoryIconUrl($category),
                        'size' => isset($stat) ? $stat['size'] : 0,
                        'type' => $this->fileInfo->getMimeType($imageName)
                    ]
                ];
            }
        }

        if (isset($data[CategoryInterface::ARTICLE_LIST_ICON])) {
            $imageName = $category->getArticleListIcon();
            unset($data[CategoryInterface::ARTICLE_LIST_ICON]);
            if ($this->fileInfo->isExist($imageName)) {
                $stat = $this->fileInfo->getStat($imageName);
                $data[CategoryInterface::ARTICLE_LIST_ICON] = [
                    [
                        'name' => $imageName,
                        'url' => $this->url->getArticleListIconUrl($category),
                        'size' => isset($stat) ? $stat['size'] : 0,
                        'type' => $this->fileInfo->getMimeType($imageName)
                    ]
                ];
            }
        }

        if (isset($data[CategoryInterface::EXTENSION_ATTRIBUTES_KEY])) {
            $data[CategoryInterface::EXTENSION_ATTRIBUTES_KEY] = $this->dataObjectProcessor->buildOutputDataArray(
                $data[CategoryInterface::EXTENSION_ATTRIBUTES_KEY],
                CategoryExtensionInterface::class
            );
        }

        return $data;
    }
}
