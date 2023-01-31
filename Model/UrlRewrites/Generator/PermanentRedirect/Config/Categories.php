<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\Config;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\AbstractGenerator;
use Aheadworks\FaqFree\Model\UrlRewrites\PathBuilder;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\OptionProvider as UrlRewriteOptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface as CategoryRepository;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\Exception\LocalizedException;

class Categories extends AbstractGenerator
{
    /**
     * @var UrlRewriteFactory
     */
    private $urlRewriteFactory;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PathBuilder
     */
    private $pathBuilder;

    /**
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param CategoryRepository $categoryRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PathBuilder $pathBuilder
     * @param Config $config
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        UrlRewriteFactory $urlRewriteFactory,
        CategoryRepository $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PathBuilder $pathBuilder,
        Config $config,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * Returns rewrites for entity
     *
     * @param mixed $oldEntityState
     * @param mixed $newEntityState
     * @param int $storeId
     * @return UrlRewrite[]
     * @throws LocalizedException
     */
    protected function getEntityRewrites($oldEntityState, $newEntityState, $storeId)
    {
        $result = [];

        $categoriesSearchCriteria = $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::IS_ENABLE, true)
            ->addFilter(CategoryInterface::STORE_IDS, $storeId)
            ->create();
        $categories = $this->categoryRepository->getList($categoriesSearchCriteria)->getItems();

        /** @var CategoryInterface $category */
        foreach ($categories as $category) {
            $requestPath = $this->pathBuilder->buildCategoryPath($oldEntityState, $category);
            $targetPath = $this->pathBuilder->buildCategoryPath($newEntityState, $category);

            if ($requestPath !== $targetPath) {
                $result[] = $this->urlRewriteFactory->create()
                    ->setRequestPath($requestPath)
                    ->setTargetPath($targetPath)
                    ->setEntityType(UrlRewriteEntityType::TYPE_CATEGORY)
                    ->setEntityId($category->getCategoryId())
                    ->setStoreId($storeId)
                    ->setRedirectType(UrlRewriteOptionProvider::PERMANENT);
            }
        }

        return $result;
    }

    /**
     * Returns processed entity type
     *
     * @return string
     */
    protected function getEntityType()
    {
        return UrlConfigMetadataModel::class;
    }

    /**
     * Checks if fields responsible for url, changed for entity
     *
     * @param mixed $oldEntityState
     * @param mixed $newEntityState
     * @return bool
     */
    protected function isNeedGenerateRewrites($oldEntityState, $newEntityState)
    {
        return $oldEntityState->getFaqRoute() !== $newEntityState->getFaqRoute()
            || $oldEntityState->getCategoryUrlSuffix() !== $newEntityState->getCategoryUrlSuffix();
    }
}
