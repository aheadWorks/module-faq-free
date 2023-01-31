<?php
namespace Aheadworks\FaqFree\Controller;

use Aheadworks\FaqFree\Model\Url;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class Router implements RouterInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var RouterInterface[]
     */
    private $routers;

    /**
     * @param Url $url
     * @param array $routers
     */
    public function __construct(
        Url $url,
        array $routers = []
    ) {
        $this->url = $url;
        $this->routers = $routers;
    }

    /**
     * Match Faq pages
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $pathInfo = (string)$request->getPathInfo();
        $path = explode('/', trim($pathInfo, '/'));
        $isFaqUrl = !empty($path[0]) && $path[0] === $this->url->getFaqRoute();

        $result = null;
        if ($isFaqUrl) {
            foreach ($this->routers as $router) {
                $result = $router->match($request);
                if ($result !== null) {
                    break;
                }
            }
        }

        return $result;
    }
}
