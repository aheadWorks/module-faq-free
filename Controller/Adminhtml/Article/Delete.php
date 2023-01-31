<?php

namespace Aheadworks\FaqFree\Controller\Adminhtml\Article;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface as ArticleRepository;

/**
 * FAQ Article Delete
 */
class Delete extends AbstractAction
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @param ArticleRepository $articleRepository
     * @param Context $context
     */
    public function __construct(
        ArticleRepository $articleRepository,
        Context $context
    ) {
        $this->articleRepository = $articleRepository;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('article_id');
        /**
         * @var Redirect $resultRedirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $this->articleRepository->deleteById($id);
                $this->messageManager->addSuccess(__('The article has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while trying to delete the article.'));

                return $resultRedirect->setPath('*/*/edit', ['article_id' => $id]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
