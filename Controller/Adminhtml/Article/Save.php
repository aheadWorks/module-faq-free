<?php

namespace Aheadworks\FaqFree\Controller\Adminhtml\Article;

use Aheadworks\FaqFree\Model\Article;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface as ArticleRepository;
use Aheadworks\FaqFree\Api\Data\ArticleInterfaceFactory as ArticleFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;

/**
 * FAQ Article Save
 */
class Save extends AbstractAction
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var ArticleFactory
     */
    private $articleFactory;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ArticleRepository $articleRepository
     * @param ArticleFactory $articleFactory
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        ArticleRepository $articleRepository,
        ArticleFactory $articleFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->articleRepository = $articleRepository;
        $this->articleFactory = $articleFactory;
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
        /**
         * @var Redirect $resultRedirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->prepareData($data);
            /**
             * @var Article $model
             */
            $model = $this->articleFactory->create();

            $id = $this->getRequest()->getParam('article_id');
            if ($id) {
                $model = $this->articleRepository->getById($id);
            }

            $model->setData($data);

            try {
                $this->articleRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the article.'));
                $this->dataPersistor->clear('aw_faq_article');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['article_id' => $model->getArticleId(), '_current' => true]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the article.'));
            }
            $this->dataPersistor->set('faq_article', $data);

            return $resultRedirect->setPath(
                '*/*/edit',
                ['article_id' => $this->getRequest()->getParam('article_id')]
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
        if (isset($data['is_enable']) && $data['is_enable'] === 'true') {
            $data['is_enable'] = 1;
        }
        if (empty($data['article_id'])) {
            $data['article_id'] = null;
        }

        return $data;
    }
}
