<?php
declare(strict_types=1);

namespace Aheadworks\FaqFree\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Aheadworks\FaqFree\Model\Flag;
use Psr\Log\LoggerInterface;

class SetFlagModuleWasInstalled implements DataPatchInterface
{
    /**
     * @param LoggerInterface $logger
     * @param Flag $flag
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Flag $flag
    ) {
    }

    /**
     * Set flag that module was installed
     *
     * @return self
     */
    public function apply()
    {
        try {
            $this->flag
                ->unsetData()
                ->setFlag(Flag::AW_FAQFREE_WAS_INSTALLED)
                ->loadSelf()
                ->setFlagData(1)
                ->save();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $this;
    }

    /**
     * Get patch aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
