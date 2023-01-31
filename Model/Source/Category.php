<?php
namespace Aheadworks\FaqFree\Model\Source;

use Aheadworks\FaqFree\Model\ResourceModel\Category\Collection;
use Magento\Framework\Data\OptionSourceInterface;

class Category implements OptionSourceInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param Collection $collection
     */
    public function __construct(
        Collection $collection
    ) {
        $this->collection = $collection;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = $this->collection->toOptionArray();
        $optionArray[] =
            [
                'value' => null,
                'label' => ''
            ];
        return $optionArray;
    }
}
