<?php

namespace Aheadworks\FaqFree\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\FaqFree\Model\ResourceModel\Category\CollectionFactory;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface as CategoryRepository;

class MassEnable extends AbstractAction
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CategoryRepository $categoryRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            foreach ($collection as $category) {
                $category->setIsEnable(true);
                $this->categoryRepository->save($category);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong while saving changes.'));
            return $resultRedirect->setPath('*/*/');
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been enabled.', $collection->getSize()));
        
        return $resultRedirect->setPath('*/*/');
    }
}
