<?php
declare(strict_types=1);

namespace Aheadworks\FaqFree\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Aheadworks\FaqFree\Model\Flag;
use Psr\Log\LoggerInterface;

class SetFlagModuleWasInstalled implements DataPatchInterface
{
    /**
     * @var Flag $flag
     */
    private $flag;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     * @param Flag $flag
     */
    public function __construct(
        LoggerInterface $logger,
        Flag $flag
    ) {
        $this->logger = $logger;
        $this->flag = $flag;
    }

    /**
     * Set flag that module was installed
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
