<?php
namespace Aheadworks\FaqFree\Controller\Adminhtml\Category;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface as CategoryRepository;

class Delete extends AbstractAction
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param CategoryRepository $categoryRepository
     * @param Context $context
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        Context $context
    ) {
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(CategoryInterface::CATEGORY_ID);

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                $this->categoryRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The category has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while trying to delete the category.'));
                return $resultRedirect->setPath('*/*/');
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
