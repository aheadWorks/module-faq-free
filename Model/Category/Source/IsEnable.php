<?php
namespace Aheadworks\FaqFree\Model\Category\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsEnable implements OptionSourceInterface
{
    /**
     * Category's Statuses
     */
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;
    
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    /**
     * Prepare category's statuses.
     *
     * @return array
     */
    private function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}
