<?php
declare(strict_types=1);

namespace Aheadworks\FaqFree\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Fieldset as FieldsetFormField;
use Aheadworks\FaqFree\Model\ThirdPartyModule\ModuleManager;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\View\Helper\Js;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class FieldsetWithMessage extends FieldsetFormField
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param Context $context
     * @param Session $authSession
     * @param Js $jsHelper
     * @param ModuleManager $moduleManager
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        ModuleManager $moduleManager,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data, $secureRenderer);
        $this->moduleManager = $moduleManager;
    }

    /**
     * Return header comment part of html for fieldset
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        $message = __('You are using the free version. Upgrade now to get: '
            . 'ChatBot, Helpfulness rating, Article questions. '
            . 'We suggest that you install paid FAQ IN ADDITION to the Free FAQ to keep all the data.');

        return $this->moduleManager->isAheadworksFaqEnabled()
            ? parent::_getHeaderCommentHtml($element)
            : '<div class="comment message">' . $message . '</div>';
    }
}
