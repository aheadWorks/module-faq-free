<?php
namespace Aheadworks\FaqFree\Controller\Router;

use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\ResourceModel\Category as ResourceCategory;
use Aheadworks\FaqFree\Model\Url;
use Aheadworks\FaqFree\Model\Router\PathUrlKeyExtractor;
use Aheadworks\FaqFree\Model\Router\UrlKeyChecker;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Aheadworks\FaqFree\Model\Router\RedirectRequestToUrl;

class Category implements RouterInterface
{
    /**
     * Category resource model
     *
     * @var ResourceCategory
     */
    private $resourceCategory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PathUrlKeyExtractor
     */
    private $pathUrlKeyExtractor;

    /**
     * @var UrlKeyChecker
     */
    private $urlKeyChecker;

    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var RedirectRequestToUrl
     */
    private $redirectRequestToUrl;

    /**
     * Category constructor.
     * @param ActionFactory $actionFactory
     * @param ResourceCategory $resourceCategory
     * @param Config $config
     * @param PathUrlKeyExtractor $pathUrlKeyExtractor
     * @param UrlKeyChecker $urlKeyChecker
     * @param RedirectRequestToUrl $redirectRequestToUrl
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResourceCategory $resourceCategory,
        Config $config,
        PathUrlKeyExtractor $pathUrlKeyExtractor,
        UrlKeyChecker $urlKeyChecker,
        RedirectRequestToUrl $redirectRequestToUrl
    ) {

        $this->actionFactory = $actionFactory;
        $this->resourceCategory = $resourceCategory;
        $this->config = $config;
        $this->pathUrlKeyExtractor = $pathUrlKeyExtractor;
        $this->urlKeyChecker = $urlKeyChecker;
        $this->redirectRequestToUrl = $redirectRequestToUrl;
    }

    /**
     * Match category page
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $pathInfo = (string)$request->getPathInfo();
        $path = explode('/', trim($pathInfo, '/'));
        $countPathParts = count($path);

        $assumeIsCategoryPage = $countPathParts === 2 && $path[1] !== Url::FAQ_SEARCH_ROUTE
            && $path[1] !== Url::FAQ_TAG_ROUTE;

        if ($assumeIsCategoryPage) {
            $categoryUrlKeyWithSuffix = $this->pathUrlKeyExtractor->getUrlKey($pathInfo);
            $categoryUrlSuffix = $this->config->getCategoryUrlSuffix();

            if ($this->urlKeyChecker->isUrlKeyEndsWithIllegalSlash($categoryUrlKeyWithSuffix, $categoryUrlSuffix)) {
                return $this->redirectRequestToUrl->getPreparedRedirect($request, trim($pathInfo, '/'));
            }
            if ($this->urlKeyChecker->isUrlKeyRequiresSlashInEnd($categoryUrlKeyWithSuffix, $categoryUrlSuffix)) {
                $url = trim($pathInfo, '/') . '/';
                return $this->redirectRequestToUrl->getPreparedRedirect($request, $url);
            }

            $categoryUrlKey = $this->pathUrlKeyExtractor->getUrlKeyWithoutSuffix($pathInfo, $categoryUrlSuffix);
            if (!$categoryUrlKey) {
                return null;
            }

            $categoryId = $this->resourceCategory->getIdByUrlKey($categoryUrlKey);
            if ($categoryId) {
                $request
                    ->setModuleName('faq')
                    ->setControllerName('category')
                    ->setActionName('index')
                    ->setParam('id', $categoryId);
            } else {
                return null;
            }
        } else {
            return null;
        }

        return $this->actionFactory->create(Forward::class, ['request' => $request]);
    }
}
