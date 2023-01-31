<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\Config;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\AbstractGenerator;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\OptionProvider as UrlRewriteOptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface as ArticleRepository;
use Aheadworks\FaqFree\Model\UrlRewrites\PathBuilder;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Articles extends AbstractGenerator
{
    /**
     * @var UrlRewriteFactory
     */
    private $urlRewriteFactory;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PathBuilder
     */
    private $pathBuilder;

    /**
     * Articles constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param ArticleRepository $articleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PathBuilder $pathBuilder
     * @param Config $config
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        UrlRewriteFactory $urlRewriteFactory,
        ArticleRepository $articleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PathBuilder $pathBuilder,
        Config $config,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->articleRepository = $articleRepository;
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

        $articlesSearchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ArticleInterface::IS_ENABLE, true)
            ->addFilter(ArticleInterface::STORE_IDS, $storeId)
            ->create();
        $articles = $this->articleRepository->getList($articlesSearchCriteria)->getItems();

        /** @var ArticleInterface $article */
        foreach ($articles as $article) {
            $requestPath = $this->pathBuilder->buildArticlePath($oldEntityState, $article);
            $targetPath = $this->pathBuilder->buildArticlePath($newEntityState, $article);

            if ($requestPath !== $targetPath) {
                $result[] = $this->urlRewriteFactory->create()
                    ->setRequestPath($requestPath)
                    ->setTargetPath($targetPath)
                    ->setEntityType(UrlRewriteEntityType::TYPE_ARTICLE)
                    ->setEntityId($article->getArticleId())
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
            || $oldEntityState->getArticleUrlSuffix() !== $newEntityState->getArticleUrlSuffix();
    }
}
