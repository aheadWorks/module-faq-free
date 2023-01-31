<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect;

use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;

abstract class AbstractGenerator
{
    /**
     * @var AbstractGenerator[]
     */
    private $subordinateEntitiesGenerators = [];

    /**
     * @var MergeDataProviderFactory
     */
    private $mergeDataProviderFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * AbstractGenerator constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param Config $config
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        Config $config,
        $subordinateEntitiesGenerators = []
    ) {
        $this->mergeDataProviderFactory = $mergeDataProviderFactory;
        $this->config = $config;
        $this->subordinateEntitiesGenerators = $subordinateEntitiesGenerators;
    }

    /**
     * Creates url rewrites based on old and new entity state
     *
     * @param mixed $oldEntityState
     * @param mixed $newEntityState
     * @param int $storeId
     * @return UrlRewrite[]
     * @throws LocalizedException
     */
    public function generate($oldEntityState, $newEntityState, $storeId)
    {
        $result = [];

        $this->validateEntity($oldEntityState);
        $this->validateEntity($newEntityState);

        if ($this->config->getSaveRewritesHistory($storeId)
            && $this->isNeedGenerateRewrites($oldEntityState, $newEntityState)
        ) {
            /** @var MergeDataProvider $mergeDataProvider */
            $mergeDataProvider = $this->mergeDataProviderFactory->create();
            $mergeDataProvider->merge(
                $this->getEntityRewrites($oldEntityState, $newEntityState, $storeId)
            );
            $mergeDataProvider->merge(
                $this->getSubordinateEntitiesRewrites($oldEntityState, $newEntityState, $storeId)
            );
            $result = $mergeDataProvider->getData();
        }

        return $result;
    }

    /**
     * Returns processed entity type
     *
     * @return string
     */
    abstract protected function getEntityType();

    /**
     * Returns rewrites for entity
     *
     * @param mixed $oldEntityState
     * @param mixed $newEntityState
     * @param int $storeId
     * @return UrlRewrite[]
     * @throws LocalizedException
     */
    abstract protected function getEntityRewrites($oldEntityState, $newEntityState, $storeId);

    /**
     * Checks if fields responsible for url, changed for entity
     *
     * @param mixed $oldEntityState
     * @param mixed $newEntityState
     * @return bool
     */
    abstract protected function isNeedGenerateRewrites($oldEntityState, $newEntityState);

    /**
     * Returns rewrites for subordinate entities(for example article subordinate for category)
     *
     * @param mixed $oldEntityState
     * @param mixed $newEntityState
     * @param int $storeId
     * @return UrlRewrite[]
     * @throws LocalizedException
     */
    private function getSubordinateEntitiesRewrites($oldEntityState, $newEntityState, $storeId)
    {
        $subordinateRewrites = [];
        /** @var AbstractGenerator $generator */
        foreach ($this->subordinateEntitiesGenerators as $generator) {
            $subordinateRewrites[] = $generator->generate($oldEntityState, $newEntityState, $storeId);
        }

        return array_merge([], ...$subordinateRewrites);
    }

    /**
     * Validate entity
     *
     * @param ArticleInterface $entity
     * @throws LocalizedException
     */
    private function validateEntity($entity)
    {
        $entityType = $this->getEntityType();
        if (!$entity instanceof $entityType) {
            throw new LocalizedException(__('For correct rewrite processing entity type must be %1', $entityType));
        }
    }
}
