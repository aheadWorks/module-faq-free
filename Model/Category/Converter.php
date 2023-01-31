<?php
namespace Aheadworks\FaqFree\Model\Category;

use Aheadworks\FaqFree\Model\Category as CategoryModel;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterfaceFactory as CategoryFactory;
use Magento\Framework\Api\DataObjectHelper;

class Converter
{
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Converter constructor.
     * @param CategoryFactory $categoryFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Converts CategoryModel model to CategoryInterface
     *
     * @param CategoryModel $model
     * @returns CategoryInterface
     */
    public function toDataObject($model)
    {
        $category = $this->categoryFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $category,
            $model->getData(),
            CategoryInterface::class
        );

        return $category;
    }
}
