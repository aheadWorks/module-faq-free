<?php
namespace Aheadworks\FaqFree\Model\ResourceModel;

use Aheadworks\FaqFree\Api\Data\TagInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Tag extends AbstractDb
{
    public const MAIN_TABLE_NAME = 'aw_faq_tag';

    /**
     * Tag constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, TagInterface::ID);
    }
}
