<?php
namespace Aheadworks\FaqFree\Model\Article\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * FAQ Article IsActive
 */
class IsActive implements OptionSourceInterface
{
    /**
     * Article's Statuses
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
     * Prepare article's statuses.
     *
     * @return array
     */
    private function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}
