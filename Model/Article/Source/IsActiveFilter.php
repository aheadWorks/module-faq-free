<?php
namespace Aheadworks\FaqFree\Model\Article\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsActiveFilter implements OptionSourceInterface
{
    /**
     * @var IsActive $isActive
     */
    private $isActive;

    /**
     * @param IsActive $isActive
     */
    public function __construct(
        IsActive $isActive
    ) {
        $this->isActive = $isActive;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->isActive->toOptionArray();
    }
}
