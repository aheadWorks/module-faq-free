<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Service;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\Entity\Article\Article
    as PermanentRedirectsGenerator;
use Aheadworks\FaqFree\Model\UrlRewrites\RewriteUpdater;
use Aheadworks\FaqFree\Model\UrlRewrites\Store\Resolver as PermanentRedirectsStoreResolver;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\UpdatedExistingRedirectsGenerator;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;

class Article
{
    /**
     * @var PermanentRedirectsGenerator
     */
    private $permanentRedirectsGenerator;

    /**
     * @var UpdatedExistingRedirectsGenerator
     */
    private $updatedExistingRedirectsGenerator;

    /**
     * @var RewriteUpdater
     */
    private $rewriteUpdater;

    /**
     * @var PermanentRedirectsStoreResolver
     */
    private $permanentRedirectsStoreResolver;

    /**
     * @var MergeDataProviderFactory
     */
    private $mergeDataProviderFactory;

    /**
     * Article constructor.
     * @param PermanentRedirectsGenerator $permanentRedirectsGenerator
     * @param RewriteUpdater $rewriteUpdater
     * @param PermanentRedirectsStoreResolver $permanentRedirectsStoreResolver
     * @param UpdatedExistingRedirectsGenerator $updatedExistingRedirectsGenerator
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     */
    public function __construct(
        PermanentRedirectsGenerator $permanentRedirectsGenerator,
        RewriteUpdater $rewriteUpdater,
        PermanentRedirectsStoreResolver $permanentRedirectsStoreResolver,
        UpdatedExistingRedirectsGenerator $updatedExistingRedirectsGenerator,
        MergeDataProviderFactory $mergeDataProviderFactory
    ) {
        $this->permanentRedirectsGenerator = $permanentRedirectsGenerator;
        $this->rewriteUpdater = $rewriteUpdater;
        $this->permanentRedirectsStoreResolver = $permanentRedirectsStoreResolver;
        $this->updatedExistingRedirectsGenerator = $updatedExistingRedirectsGenerator;
        $this->mergeDataProviderFactory = $mergeDataProviderFactory;
    }

    /**
     * Updates permanent redirects
     *
     * @param ArticleInterface $article
     * @param ArticleInterface $origArticle
     * @throws LocalizedException
     * @return bool
     */
    public function updatePermanentRedirects($article, $origArticle)
    {
        $stores = $this->permanentRedirectsStoreResolver->resolve(
            $origArticle->getStoreIds(),
            $article->getStoreIds()
        );

        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        foreach ($stores as $store) {
            $newRedirects = $this->permanentRedirectsGenerator->generate($origArticle, $article, $store);
            $updatedExistingRedirects = $this->updatedExistingRedirectsGenerator->generate($newRedirects);
            $mergeDataProvider->merge($newRedirects);
            $mergeDataProvider->merge($updatedExistingRedirects);
        }

        $allRedirects = $mergeDataProvider->getData();
        $this->rewriteUpdater->update($allRedirects);

        return true;
    }

    /**
     * Update controller rewrites
     *
     * @param ArticleInterface $article
     * @return ArticleInterface
     */
    public function updateControllerRewrites($article)
    {
        //Can be used for refactoring of seo url processing (it is being processed without participation
        //rewrites in class Aheadworks\FaqFree\Controller\Router at the moment). Here you can delete all old
        //controller rewrites for all stores and create new rewrites for appropriate stores

        return $article;
    }
}
