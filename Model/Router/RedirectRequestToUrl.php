<?php
namespace Aheadworks\FaqFree\Model\Router;

use Magento\Framework\App\Action\Redirect;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\UrlInterface;
use Magento\UrlRewrite\Model\OptionProvider;

class RedirectRequestToUrl
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var UrlInterface
     */
    private $urlCreator;

    /**
     * RedirectToClearUrl constructor.
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     * @param UrlInterface $urlCreator
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        UrlInterface $urlCreator
    ) {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->urlCreator = $urlCreator;
    }

    /**
     * Returns prepared redirect to url
     *
     * @param RequestInterface $request
     * @param string $url
     * @return ActionInterface
     */
    public function getPreparedRedirect(RequestInterface $request, string $url)
    {
        $this->response->setRedirect(
            $this->urlCreator->getDirectUrl($url),
            OptionProvider::PERMANENT
        );
        $request->setDispatched(true);

        return $this->actionFactory->create(Redirect::class);
    }
}
