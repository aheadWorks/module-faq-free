<?php
namespace Aheadworks\FaqFree\Block\Article;

use Aheadworks\FaqFree\Block\AbstractArticleTags;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template;

class Tags extends AbstractArticleTags
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param Template\Context $context
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->request = $request;
    }

    /**
     * Retrieve article instance id
     *
     * @return int
     */
    public function getArticleId()
    {
        return $this->request->getParam('id');
    }
}
