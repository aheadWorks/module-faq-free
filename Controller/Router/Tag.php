<?php
namespace Aheadworks\FaqFree\Controller\Router;

use Aheadworks\FaqFree\Model\Url;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class Tag implements RouterInterface
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
     * Match tag page
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $pathInfo = (string)$request->getPathInfo();
        $path = explode('/', trim($pathInfo, '/'));
        $countPathParts = count($path);

        $assumeIsTagPage = $countPathParts === 2 && $path[1] === Url::FAQ_TAG_ROUTE;

        if ($assumeIsTagPage) {
            $request
                ->setModuleName('faq')
                ->setControllerName('tag')
                ->setActionName('index');
        } else {
            return null;
        }

        return $this->actionFactory->create(Forward::class, ['request' => $request]);
    }
}
