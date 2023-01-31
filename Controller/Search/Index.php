<?php
namespace Aheadworks\FaqFree\Controller\Search;

use Aheadworks\FaqFree\Controller\AbstractAction;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\Redirect;
use Aheadworks\FaqFree\Model\Url;
use Aheadworks\FaqFree\Model\Config;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;

/**
 * FAQ search results page
 */
class Index extends AbstractAction
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @var Url
     */
    private $url;

    /**
     * Index constructor.
     * @param Url $url
     * @param Config $config
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Url $url,
        Config $config,
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $storeManager);

        $this->url = $url;
        $this->config = $config;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * View FAQ search results action
     *
     * @return Page|Redirect|Forward
     */
    public function _execute()
    {
        if (!$this->config->isFaqSearchEnabled()) {
            /** @var Forward $forward */
            $forward = $this->resultForwardFactory->create();
            return $forward->setModule('cms')->setController('noroute')->forward('index');
        }

        $searchQuery = $this->getRequest()->getParam(Url::FAQ_QUERY_PARAM);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
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
                    'label' => $this->config->getFaqName(),
                    'title'=>__('Go to %1', $this->config->getFaqName()),
                    'link'=> $this->url->getFaqHomeUrl()
                ]
            )->addCrumb(
                'search',
                [
                    'label' => __('Search results for: "%1"', $searchQuery)
                ]
            );

        $pageConfig = $resultPage->getConfig();
        $pageConfig->getTitle()->set(__('Search results for: "%1"', $searchQuery));
        $pageConfig->setMetadata('robots', 'noindex');

        return $resultPage;
    }
}
