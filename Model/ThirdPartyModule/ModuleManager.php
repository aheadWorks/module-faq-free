<?php
declare(strict_types=1);

namespace Aheadworks\FaqFree\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;

class ModuleManager
{
    private const AHEADWORKS_FAQ_MODULE_NAME = 'Aheadworks_Faq';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if Aheadworks Faq module is enabled
     *
     * @return bool
     */
    public function isAheadworksFaqEnabled()
    {
        return $this->moduleList->has(self::AHEADWORKS_FAQ_MODULE_NAME);
    }
}
