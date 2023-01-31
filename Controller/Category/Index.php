<?php
namespace Aheadworks\FaqFree\Controller\Category;

use Aheadworks\FaqFree\Controller\AbstractAction;
use Aheadworks\FaqFree\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;
use Aheadworks\FaqFree\Model\Url;
use Aheadworks\FaqFree\Model\Category;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;
use Aheadworks\FaqFree\Model\Metadata\MetadataInterface;

/**
 * FAQ category page view
 */
class Index extends AbstractAction
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

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
        CategoryRepositoryInterface $categoryRepository,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager,
        MetadataInterface $metadataProvider
    ) {
        parent::__construct($context, $storeManager);
        $this->url = $url;
        $this->config = $config;
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryRepository = $categoryRepository;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->config = $config;
        $this->metadataProvider = $metadataProvider;
    }

    /**
     * View FAQ category page action
     *
     * @return Page|Redirect|Forward
     */
    public function _execute()
    {
        $categoryId = $this->getRequest()->getParam('id');

        /** @var Category $category */
        $category = $this->categoryRepository->getById($categoryId);

        if (!$category->getIsEnable()) {
            /** @var Forward $forward */
            $forward = $this->resultForwardFactory->create();
            return $forward->setModule('cms')->setController('noroute')->forward('index');
        }

        if (!array_intersect($category->getStoreIds(), $this->getCurrentStores())) {
            return $this->redirectWithErrorMessage();
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $pageConfig = $resultPage->getConfig();
        $pageConfig->getTitle()->set($this->metadataProvider->getTitle($category));
        $pageConfig->setMetaTitle($this->metadataProvider->getMetaTitle($category));
        $pageConfig->setDescription($this->metadataProvider->getMetaDescription($category));
        $pageConfig->setKeywords($this->metadataProvider->getMetaKeywords($category));

        if ($this->config->getCategoryCanonicalTag()) {
            $resultPage->getConfig()->addRemotePageAsset(
                $this->url->getCategoryUrl($category),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs')
            ->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title'=>__('Go to store homepage'),
                    'link'=> $this->url->getBaseUrl()
                ]
            )
            ->addCrumb(
                'faq',
                [
                    'label' => $this->config->getFaqName(),
                    'title'=>__('Go to %1', $this->config->getFaqName()),
                    'link'=> $this->url->getFaqHomeUrl()
                ]
            );

        $pathIds = explode('/', (string)$category->getPath());
        $pathIds = array_diff($pathIds, [$categoryId]);
        foreach ($pathIds as $pathId) {
            $parentCategory = $this->categoryRepository->getById($pathId);
            $breadcrumbs->addCrumb(
                $parentCategory->getUrlKey(),
                [
                    'label' => $parentCategory->getName(),
                    'title'=>__('Go to %1', $parentCategory->getName()),
                    'link'=> $this->url->getCategoryUrl($parentCategory)
                ]
            );
        }
        $breadcrumbs->addCrumb(
            $category->getUrlKey(),
            [
                'label' => $category->getName(),
            ]
        );

        return $resultPage;
    }
}
