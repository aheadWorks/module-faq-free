<?php
namespace Aheadworks\FaqFree\Model;

use Aheadworks\FaqFree\Api\Data\TagInterface;
use Aheadworks\FaqFree\Api\Data\TagInterfaceFactory;
use Aheadworks\FaqFree\Api\Data\TagSearchResultsInterface;
use Aheadworks\FaqFree\Api\Data\TagSearchResultsInterfaceFactory;
use Aheadworks\FaqFree\Api\TagRepositoryInterface;
use Aheadworks\FaqFree\Model\ResourceModel\Tag as ResourceTag;
use Aheadworks\FaqFree\Model\ResourceModel\Tag\Collection;
use Aheadworks\FaqFree\Model\ResourceModel\Tag\CollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\LocalizedException;

class TagRepository implements TagRepositoryInterface
{
    /**
     * @var ResourceTag
     */
    private $resource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var TagInterfaceFactory
     */
    private $tagFactory;

    /**
     * @var TagSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var array
     */
    private $registry;

    /**
     * @param ResourceTag $resource
     * @param CollectionFactory $collectionFactory
     * @param TagInterfaceFactory $tagFactory
     * @param TagSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceTag $resource,
        CollectionFactory $collectionFactory,
        TagInterfaceFactory $tagFactory,
        TagSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->tagFactory = $tagFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Save tag
     *
     * @param TagInterface $tag
     * @return TagInterface
     * @throws LocalizedException
     */
    public function save(TagInterface $tag)
    {
        try {
            $this->resource->save($tag);
            $id = $tag->getId();
            $this->registry[$this->getRegistryKey($id)] = $tag;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $tag;
    }

    /**
     * Retrieve tag
     *
     * @param int $id
     * @return TagInterface
     * @throws LocalizedException
     */
    public function get($id)
    {
        $registryKey = $this->getRegistryKey($id);
        if (!isset($this->registry[$registryKey])) {
            $tag = $this->tagFactory->create();
            $this->resource->load($tag, $id);
            if (!$tag->getId()) {
                throw NoSuchEntityException::singleField(TagInterface::ID, $id);
            }
            $this->registry[$registryKey] = $tag;
        }
        return $this->registry[$registryKey];
    }

    /**
     * Delete tag
     *
     * @param TagInterface $tag
     * @throws LocalizedException
     */
    public function delete(TagInterface $tag)
    {
        try {
            $tagId = $tag->getId();
            $this->resource->delete($tag);
            $registryKey = $this->getRegistryKey($tagId);
            if (isset($this->registry[$registryKey])) {
                unset($this->registry[$registryKey]);
            }
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
    }

    /**
     * Delete tag by ID
     *
     * @param int $id
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($id)
    {
        $this->delete($this->get($id));
    }

    /**
     * Retrieve tags matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return TagSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, TagInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);
        /** @var TagSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $tags = [];
        /** @var Tag $item */
        foreach ($collection->getItems() as $item) {
            $tags[] = $this->getTagDataObject($item);
        }
        $searchResults
            ->setTotalCount($collection->getSize())
            ->setItems($tags);

        return $searchResults;
    }

    /**
     * Normalize registry cache key for PHP 8.4+ (null array offsets are deprecated)
     *
     * @param int|string|null $id
     * @return int|string
     */
    private function getRegistryKey($id)
    {
        return $id ?? '';
    }

    /**
     * Retrieves data object using model
     *
     * @param Tag $tag
     * @return TagInterface
     */
    private function getTagDataObject($tag)
    {
        /** @var TagInterface $tagDataObject */
        $tagDataObject = $this->tagFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $tagDataObject,
            $this->dataObjectProcessor->buildOutputDataArray($tag, TagInterface::class),
            TagInterface::class
        );
        return $tagDataObject;
    }
}
