<?php
namespace Aheadworks\FaqFree\Block\Category;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Magento\Framework\View\Element\Template;
use Aheadworks\FaqFree\Model\Page\Request\InstanceCreator;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\FaqFree\Model\Config;

class ChildItems extends Template
{
    /**
     * @var InstanceCreator
     */
    private $pageInstanceCreator;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param Config $config
     * @param InstanceCreator $pageInstanceCreator
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        InstanceCreator $pageInstanceCreator,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->pageInstanceCreator = $pageInstanceCreator;
        $this->config = $config;
    }

    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml()
    {
        if ($this->isBlockVisible()) {
            return parent::toHtml();
        }

        return '';
    }

    /**
     * Check if block is visible
     *
     * @return bool
     */
    public function isBlockVisible()
    {
        $result = false;

        /** @var CategoryInterface $currentCategory */
        $currentCategory = $this->pageInstanceCreator->getCurrentCategory();
        if ($currentCategory) {
            if ((!$currentCategory->getIsShowChildrenDefault() && $currentCategory->getIsShowChildren())
                || ($currentCategory->getIsShowChildrenDefault()) && $this->config->getIsShowCategoryChildren()) {
                $result = true;
            }
        }

        return $result;
    }
}
