<?php

namespace Aheadworks\FaqFree\Controller\Adminhtml\Category;

use Aheadworks\FaqFree\Model\Category;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface as CategoryRepository;
use Aheadworks\FaqFree\Api\Data\CategoryInterfaceFactory as CategoryFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Backend\Model\View\Result\Page;

/**
 * FAQ Category Edit
 */
class Edit extends AbstractAction
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param CategoryRepository $categoryRepository
     * @param CategoryFactory $categoryFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        CategoryRepository $categoryRepository,
        CategoryFactory $categoryFactory,
        PageFactory $resultPageFactory
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return Page
     */
    private function initAction()
    {
        /**
         * @var Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_FaqFree::category')
            ->addBreadcrumb(__('FAQ'), __('FAQ'))
            ->addBreadcrumb(__('Manage Categories'), __('Manage Categories'));
        return $resultPage;
    }

    /**
     * Edit Category page
     *
     * @return Page|Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @throws LocalizedException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('category_id');
        /** @var Category $model */
        $model = $this->categoryFactory->create();

        if ($id) {
            try {
                $model = $this->categoryRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                /**
                 * Redirect $resultRedirect
                 */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        /**
         * @var Page $resultPage
         */
        $resultPage = $this->initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Category') : __('New Category'),
            $id ? __('Edit Category') : __('New Category')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getCategoryId() ? $model->getName() : __('New Category'));

        return $resultPage;
    }
}
