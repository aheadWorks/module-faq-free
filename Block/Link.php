<?php
namespace Aheadworks\FaqFree\Block;

class Link extends MenuItem
{
    /**
     * Get template
     *
     * @return string|false
     */
    public function getTemplate()
    {
        if ($this->config->isDisabledFaqForCurrentCustomer()) {
            return false;
        } else {
            return parent::getTemplate();
        }
    }
}
