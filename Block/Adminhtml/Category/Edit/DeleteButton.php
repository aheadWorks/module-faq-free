<?php

namespace Aheadworks\FaqFree\Block\Adminhtml\Category\Edit;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface;

class DeleteButton implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->context = $context;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get delete button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $categoryId = $this->getCategoryId();
        if ($this->getCategoryId()) {
            $data = [
                'label' => __('Delete Category'),
                'class' => 'delete',
                'on_click' => sprintf(
                    "deleteConfirm('%s', '%s')",
                    __('Are you sure you want to do this?'),
                    $this->context->getUrlBuilder()->getUrl('*/*/delete', ['category_id' => $categoryId])
                ),
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * Return Faq category ID
     *
     * @return int|null
     */
    public function getCategoryId()
    {
        try {
            $category = $this->categoryRepository->getById(
                $this->context->getRequest()->getParam('category_id')
            );

            return $category->getCategoryId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
