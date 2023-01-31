<?php
namespace Aheadworks\FaqFree\Model\Source\Product\Attribute\Articles;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Dummy extends AbstractSource
{
    /**
     * Return empty array when module is disabled
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [];
    }
}
