<?php

namespace Aheadworks\FaqFree\Controller\Adminhtml\Article;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;

/**
 * FAQ Article Index
 */
class Index extends AbstractAction
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Article list page
     *
     * @return Page
     */
    public function execute()
    {
        /**
         * @var Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_FaqFree::article');
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Articles'));
        return $resultPage;
    }
}
