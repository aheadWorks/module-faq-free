<?php
namespace Aheadworks\FaqFree\Controller\Router;

use Aheadworks\FaqFree\Model\Url;
use Aheadworks\FaqFree\Model\Router\PathUrlKeyExtractor;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Aheadworks\FaqFree\Model\Router\RedirectRequestToUrl;

class Index implements RouterInterface
{
    /**
     * @var PathUrlKeyExtractor
     */
    private $pathUrlKeyExtractor;

    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var RedirectRequestToUrl
     */
    private $redirectRequestToUrl;

    /**
     * @var Url
     */
    private $url;

    /**
     * Index constructor.
     * @param ActionFactory $actionFactory
     * @param PathUrlKeyExtractor $pathUrlKeyExtractor
     * @param RedirectRequestToUrl $redirectRequestToUrl
     * @param Url $url
     */
    public function __construct(
        ActionFactory $actionFactory,
        PathUrlKeyExtractor $pathUrlKeyExtractor,
        RedirectRequestToUrl $redirectRequestToUrl,
        Url $url
    ) {
        $this->actionFactory = $actionFactory;
        $this->pathUrlKeyExtractor = $pathUrlKeyExtractor;
        $this->redirectRequestToUrl = $redirectRequestToUrl;
        $this->url = $url;
    }

    /**
     * Match faq index page
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $pathInfo = (string)$request->getPathInfo();
        $path = explode('/', trim($pathInfo, '/'));
        $countPathParts = count($path);

        $assumeIsIndexPage = $countPathParts === 1;

        if ($assumeIsIndexPage) {
            $faqUrlKey = (string)$this->pathUrlKeyExtractor->getUrlKey($pathInfo);
            if (trim((string)$this->url->getFullFaqRoute(), '/') == trim($faqUrlKey, '/')
                && $this->url->getFullFaqRoute() != $faqUrlKey
            ) {
                return $this->redirectRequestToUrl->getPreparedRedirect($request, $this->url->getFullFaqRoute());
            }

            $request
                ->setModuleName('faq')
                ->setControllerName('index')
                ->setActionName('index');
        } else {
            return null;
        }

        return $this->actionFactory->create(Forward::class, ['request' => $request]);
    }
}
