<?php
namespace Aheadworks\FaqFree\Block\Adminhtml\Category\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndContinue implements ButtonProviderInterface
{
    /**
     * Get save and continue button data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit', 'back' => 'edit'],
                ],
            ],
            'sort_order' => 30,
        ];
    }
}
