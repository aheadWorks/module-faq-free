<?php
declare(strict_types=1);

namespace Aheadworks\FaqFree\Model;

use Magento\Framework\Flag as FrameworkFlag;

class Flag extends FrameworkFlag
{
    public const AW_FAQFREE_WAS_INSTALLED = 'aw_faqfree_was_installed';

    /**
     * Set flag code
     *
     * @param string $code
     * @return $this
     */
    public function setFlag(string $code): self
    {
        $this->_flagCode = $code;
        return $this;
    }
}
