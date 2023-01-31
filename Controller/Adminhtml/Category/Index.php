<?php

namespace Aheadworks\FaqFree\Controller\Adminhtml\Category;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;

/**
 * FAQ category list controller
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
     * Categories list page
     *
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_FaqFree::category');
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Categories'));
        return $resultPage;
    }
}
