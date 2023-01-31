<?php
namespace Aheadworks\FaqFree\Block;

use Magento\Framework\View\Element\Html\Link as LinkElement;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\FaqFree\Model\Url;
use Aheadworks\FaqFree\Model\Config;

class MenuItem extends LinkElement
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Context $context
     * @param Url $url
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Url $url,
        Config $config,
        array $data = []
    ) {
        $this->url = $url;
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve FAQ Homepage URL
     *
     * @return string
     */
    public function getHref()
    {
        return $this->url->getFaqHomeUrl();
    }

    /**
     * Retrieve FAQ Storefront Name
     *
     * @return string
     */
    public function getFaqName()
    {
        return $this->config->getFaqName();
    }
}
