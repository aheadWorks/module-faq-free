<?php
namespace Aheadworks\FaqFree\Model\Article;

use Aheadworks\FaqFree\Model\Article as ArticleModel;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterfaceFactory as ArticleFactory;
use Magento\Framework\Api\DataObjectHelper;

class Converter
{
    /**
     * @var ArticleFactory
     */
    private $articleFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param ArticleFactory $articleFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        ArticleFactory $articleFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->articleFactory = $articleFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Converts ArticleModel model to ArticleInterface
     *
     * @param ArticleModel $model
     * @return mixed
     */
    public function toDataObject($model)
    {
        $article = $this->articleFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $article,
            $model->getData(),
            ArticleInterface::class
        );

        return $article;
    }
}
