<?php
declare(strict_types=1);

namespace Aheadworks\FaqFree\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Aheadworks\FaqFree\Model\ThirdPartyModule\ModuleManager;

class DisabledField extends Column
{
    /** @var ModuleManager */
    private $moduleManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ModuleManager $moduleManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ModuleManager $moduleManager,
        array $components = [],
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        if (!$this->moduleManager->isAheadworksFaqEnabled()) {
            $config = $this->getData('config');
            $config['tooltip'] = __('Upgrade to unlock');
            $config['visible'] = true;
            $config['headerTmpl'] = 'Aheadworks_FaqFree/ui/grid/columns/disabled-text';
            $this->setData('config', $config);
        }

        parent::prepare();
    }
}
