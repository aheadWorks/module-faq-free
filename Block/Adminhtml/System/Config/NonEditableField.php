<?php
declare(strict_types=1);

namespace Aheadworks\FaqFree\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Config\Block\System\Config\Form\Field as ConfigFormField;
use Magento\Backend\Block\Template\Context;
use Aheadworks\FaqFree\Model\ThirdPartyModule\ModuleManager;

class NonEditableField extends ConfigFormField
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param Context $context
     * @param ModuleManager $moduleManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        ModuleManager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleManager = $moduleManager;
    }

    /**
     * Retrieve element HTML markup
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $output = '';
        if (!$this->moduleManager->isAheadworksFaqEnabled()) {
            $element->setInherit(true);
            $element->setDisabled('disabled');
            $element->setData('readonly', 1);
            $element->setTooltip(__('Upgrade to unlock'));
            $output = parent::render($element);
        }

        return $output;
    }

    /**
     * Check if inheritance checkbox has to be rendered
     *
     * @param AbstractElement $element
     * @return bool
     */
    protected function _isInheritCheckboxRequired(AbstractElement $element)
    {
        return false;
    }
}
