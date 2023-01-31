<?php

namespace Aheadworks\FaqFree\Block\Adminhtml\Article\Edit;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface;

/**
 * FAQ Article DeleteButton
 */
class DeleteButton implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @param Context $context
     * @param ArticleRepositoryInterface $articleRepository
     */
    public function __construct(
        Context $context,
        ArticleRepositoryInterface $articleRepository
    ) {
        $this->context = $context;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Get delete button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $articleId = $this->getArticleId();
        if ($this->getArticleId()) {
            $data = [
                'label' => __('Delete Article'),
                'class' => 'delete',
                'on_click' => sprintf(
                    "deleteConfirm('%s', '%s')",
                    __('Are you sure you want to do this?'),
                    $this->context->getUrlBuilder()->getUrl('*/*/delete', ['article_id' => $articleId])
                ),
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * Return Faq article ID
     *
     * @return int|null
     */
    public function getArticleId()
    {
        try {
            return $this->articleRepository
                ->getById($this->context->getRequest()->getParam('article_id'))
                ->getArticleId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
