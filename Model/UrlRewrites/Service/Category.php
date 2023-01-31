<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Service;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\Entity\Category\Category
    as PermanentRedirectsGenerator;
use Aheadworks\FaqFree\Model\UrlRewrites\RewriteUpdater;
use Aheadworks\FaqFree\Model\UrlRewrites\Store\Resolver as PermanentRedirectsStoreResolver;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\UpdatedExistingRedirectsGenerator;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;

class Category
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
     * Category constructor.
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
     * @param CategoryInterface $category
     * @param CategoryInterface $origCategory
     * @throws LocalizedException
     * @return bool
     */
    public function updatePermanentRedirects($category, $origCategory)
    {
        $stores = $this->permanentRedirectsStoreResolver->resolve(
            $origCategory->getStoreIds(),
            $category->getStoreIds()
        );

        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        foreach ($stores as $store) {
            $newRedirects = $this->permanentRedirectsGenerator->generate($origCategory, $category, $store);
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
     * @param CategoryInterface $category
     * @return CategoryInterface
     */
    public function updateControllerRewrites($category)
    {
        //Can be used for refactoring of seo url processing (it is being processed without participation
        //rewrites in class Aheadworks\FaqFree\Controller\Router at the moment). Here you can delete all old
        //controller rewrites for all stores and create new rewrites for appropriate stores

        return $category;
    }
}
