<?php

namespace Aheadworks\FaqFree\Controller\Index;

use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Aheadworks\FaqFree\Controller\AbstractAction;
use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\Url;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\FaqFree\Model\Metadata\MetadataInterface;

/**
 * FAQ home page view
 */
class Index extends AbstractAction
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

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
     * @param StoreManagerInterface $storeManager
     * @param ForwardFactory $resultForwardFactory
     * @param MetadataInterface $metadataProvider
     */
    public function __construct(
        Url $url,
        Config $config,
        Context $context,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager,
        ForwardFactory $resultForwardFactory,
        MetadataInterface $metadataProvider
    ) {
        parent::__construct($context, $storeManager);
        $this->url = $url;
        $this->config = $config;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->metadataProvider = $metadataProvider;
    }

    /**
     * View FAQ homepage action
     *
     * @return Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function _execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $pageConfig = $resultPage->getConfig();
        $pageConfig->getTitle()->set($this->metadataProvider->getTitle());
        $pageConfig->setMetaTitle($this->metadataProvider->getMetaTitle());
        $pageConfig->setDescription($this->metadataProvider->getMetaDescription());
        $pageConfig->setKeywords($this->metadataProvider->getMetaKeywords());

        $pageConfig->addBodyClass('aw-columns-' . $this->config->getDefaultNumberOfColumnsToDisplay());
        $resultPage->getLayout()->getBlock('breadcrumbs')
            ->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title'=>__('Go to store homepage'),
                    'link'=> $this->url->getBaseUrl()
                ]
            )->addCrumb(
                'faq',
                [
                    'label' => $this->config->getFaqName()
                ]
            );
        return $resultPage;
    }
}
