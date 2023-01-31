<?php

namespace Aheadworks\FaqFree\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;

abstract class AbstractAction extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Aheadworks_FaqFree::category';
}
