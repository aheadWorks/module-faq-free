<?php

namespace Aheadworks\FaqFree\Controller\Adminhtml\Category;

use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;

/**
 * FAQ Category NewAction
 */
class NewAction extends AbstractAction
{
    /**
     * @var Forward
     */
    private $resultForwardFactory;

    /**
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Forward to edit
     *
     * @return Forward
     */
    public function execute()
    {
        /**
         * @var Forward $resultForward
         */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
