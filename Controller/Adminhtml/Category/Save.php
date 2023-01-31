<?php

namespace Aheadworks\FaqFree\Controller\Adminhtml\Category;

use Aheadworks\FaqFree\Api\CategoryRepositoryInterface as CategoryRepository;
use Aheadworks\FaqFree\Api\Data\CategoryInterfaceFactory as CategoryFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;

class Save extends AbstractAction
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param CategoryRepository $categoryRepository
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        CategoryRepository $categoryRepository,
        CategoryFactory $categoryFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $preparedData = $this->prepareData($data);
            /** @var \Aheadworks\FaqFree\Model\Category $category */
            $category = $this->categoryFactory->create();

            $id = $this->getRequest()->getParam('category_id');
            if ($id) {
                $category = $this->categoryRepository->getById($id);
            }

            $category->setData($preparedData);

            try {
                $this->categoryRepository->save($category);
                $this->messageManager->addSuccessMessage(__('You saved the category.'));
                $this->dataPersistor->clear('aw_faq_category');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['category_id' => $category->getCategoryId(), '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the category.'));
            }

            $this->dataPersistor->set('faq_category', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['category_id' => $this->getRequest()->getParam('category_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Prepare data before save
     *
     * @param array $data
     * @return array
     */
    private function prepareData($data)
    {
        if (isset($data['category_icon']) && is_array($data['category_icon'])) {
            if (!empty($data['category_icon']['delete'])) {
                $data['category_icon'] = null;
            } else {
                if (isset($data['category_icon'][0]['name'])) {
                    $data['category_icon'] = $data['category_icon'][0]['name'];
                } else {
                    unset($data['category_icon']);
                }
            }
        }
        
        if (isset($data['article_list_icon']) && is_array($data['article_list_icon'])) {
            if (!empty($data['article_list_icon']['delete'])) {
                $data['article_list_icon'] = null;
            } else {
                if (isset($data['article_list_icon'][0]['name'])) {
                    $data['article_list_icon'] = $data['article_list_icon'][0]['name'];
                } else {
                    unset($data['article_list_icon']);
                }
            }
        }

        return $data;
    }
}
