<?php

namespace Aheadworks\FaqFree\Controller\Adminhtml\Article;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\FaqFree\Model\ResourceModel\Article\CollectionFactory;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface as ArticleRepository;

/**
 * FAQ Article MassDisable
 */
class MassDisable extends AbstractAction
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
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ArticleRepository $articleRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->articleRepository = $articleRepository;
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

        try {
            foreach ($collection as $item) {
                $item->setIsEnable(false);
                $this->articleRepository->save($item);
            }

            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been disabled.', $collection->getSize())
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving changes.'));
        }

        /**
         * @var Redirect $resultRedirect
         */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
