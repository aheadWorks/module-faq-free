<?php
namespace Aheadworks\FaqFree\Model\Category\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsEnableFilter implements OptionSourceInterface
{
    /**
     * @var IsEnable
     */
    private $isEnable;

    /**
     * @param IsEnable $isEnable
     */
    public function __construct(IsEnable $isEnable)
    {
        $this->isEnable = $isEnable;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->isEnable->toOptionArray();
    }
}
