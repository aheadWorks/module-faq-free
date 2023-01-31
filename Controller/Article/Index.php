<?php

namespace Aheadworks\FaqFree\Controller\Article;

use Aheadworks\FaqFree\Controller\AbstractAction;
use Aheadworks\FaqFree\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;
use Aheadworks\FaqFree\Model\Url;
use Aheadworks\FaqFree\Model\Article;
use Aheadworks\FaqFree\Model\Category;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;
use Aheadworks\FaqFree\Model\Metadata\MetadataInterface;

/**
 * FAQ article page view
 */
class Index extends AbstractAction
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @var MetadataInterface
     */
    private $metadataProvider;

    /**
     * @param Url $url
     * @param Config $config
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ArticleRepositoryInterface $articleRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ForwardFactory $resultForwardFactory
     * @param StoreManagerInterface $storeManager
     * @param MetadataInterface $metadataProvider
     */
    public function __construct(
        Url $url,
        Config $config,
        Context $context,
        PageFactory $resultPageFactory,
        ArticleRepositoryInterface $articleRepository,
        CategoryRepositoryInterface $categoryRepository,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager,
        MetadataInterface $metadataProvider
    ) {
        parent::__construct($context, $storeManager);
        $this->url = $url;
        $this->config = $config;
        $this->resultPageFactory = $resultPageFactory;
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->metadataProvider = $metadataProvider;
    }

    /**
     * View FAQ article page action
     *
     * @return Page|Redirect|Forward
     * @throws LocalizedException
     */
    public function _execute()
    {
        $articleId = $this->getRequest()->getParam('id');
        $categoryId = $this->getRequest()->getParam('category_id');

        /** @var Article $article */
        $article = $this->articleRepository->getById($articleId);
        if (!$article->getIsEnable()) {
            /** @var Forward $forward */
            $forward = $this->resultForwardFactory->create();
            return $forward->setModule('cms')->setController('noroute')->forward('index');
        }

        if (!array_intersect($article->getStoreIds(), $this->getCurrentStores())) {
            return $this->redirectWithErrorMessage();
        }

        /** @var Category $articleCategory */
        $articleCategory = $this->categoryRepository->getById($article->getCategoryId());

        if ((!$categoryId && $articleCategory->getIsEnable())
            || ($categoryId && $categoryId !== $articleCategory->getCategoryId())
            || ($categoryId && !$articleCategory->getIsEnable())) {
            /** @var Forward $forward */
            $forward = $this->resultForwardFactory->create();
            return $forward->setModule('cms')->setController('noroute')->forward('index');
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $pageConfig = $resultPage->getConfig();
        $pageConfig->getTitle()->set($this->metadataProvider->getTitle($article));
        $pageConfig->setMetaTitle($this->metadataProvider->getMetaTitle($article));
        $pageConfig->setDescription($this->metadataProvider->getMetaDescription($article));
        $pageConfig->setKeywords($this->metadataProvider->getMetaKeywords($article));

        if ($this->config->getArticleCanonicalTag()) {
            $resultPage->getConfig()->addRemotePageAsset(
                $this->url->getArticleUrl($article),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

        /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbs */
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to store homepage'),
                    'link'=>$this->url->getBaseUrl()
                ]
            );

            $breadcrumbs->addCrumb(
                'faq',
                [
                    'label' => $this->config->getFaqName(),
                    'title'=>__('Go to %1', $this->config->getFaqName()),
                    'link'=> $this->url->getFaqHomeUrl()
                ]
            );

            if ($categoryId && $articleCategory->getIsEnable()) {
                $breadcrumbs->addCrumb(
                    'category',
                    [
                        'label' => $articleCategory->getName(),
                        'title'=>__('Go to %1', $articleCategory->getName()),
                        'link'=> $this->url->getCategoryUrl($articleCategory)
                    ]
                );
            }

            $breadcrumbs->addCrumb(
                'article',
                [
                    'label' => $article->getTitle()
                ]
            );
        }

        return $resultPage;
    }
}
