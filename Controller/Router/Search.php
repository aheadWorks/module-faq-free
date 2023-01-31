<?php
namespace Aheadworks\FaqFree\Controller\Router;

use Aheadworks\FaqFree\Model\Url;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class Search implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * Search constructor.
     * @param ActionFactory $actionFactory
     */
    public function __construct(
        ActionFactory $actionFactory
    ) {
        $this->actionFactory = $actionFactory;
    }

    /**
     * Match search page
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $pathInfo = (string)$request->getPathInfo();
        $path = explode('/', trim($pathInfo, '/'));
        $countPathParts = count($path);

        $assumeIsSearchPage = $countPathParts === 2 && $path[1] === Url::FAQ_SEARCH_ROUTE;

        if ($assumeIsSearchPage) {
            $request
                ->setModuleName('faq')
                ->setControllerName('search')
                ->setActionName('index');
        } else {
            return null;
        }

        return $this->actionFactory->create(Forward::class, ['request' => $request]);
    }
}
