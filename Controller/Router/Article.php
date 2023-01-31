<?php
namespace Aheadworks\FaqFree\Controller\Router;

use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\ResourceModel\Article as ResourceArticle;
use Aheadworks\FaqFree\Model\ResourceModel\Category as ResourceCategory;
use Aheadworks\FaqFree\Model\Url;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Aheadworks\FaqFree\Model\Router\PathUrlKeyExtractor;
use Aheadworks\FaqFree\Model\Router\UrlKeyChecker;
use Aheadworks\FaqFree\Model\Router\RedirectRequestToUrl;

class Article implements RouterInterface
{
    /**
     * Article resource model
     *
     * @var ResourceArticle
     */
    private $resourceArticle;

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
     * Article constructor.
     * @param ActionFactory $actionFactory
     * @param ResourceArticle $resourceArticle
     * @param ResourceCategory $resourceCategory
     * @param Config $config
     * @param PathUrlKeyExtractor $pathUrlKeyExtractor
     * @param UrlKeyChecker $urlKeyChecker
     * @param RedirectRequestToUrl $redirectRequestToUrl
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResourceArticle $resourceArticle,
        ResourceCategory $resourceCategory,
        Config $config,
        PathUrlKeyExtractor $pathUrlKeyExtractor,
        UrlKeyChecker $urlKeyChecker,
        RedirectRequestToUrl $redirectRequestToUrl
    ) {

        $this->actionFactory = $actionFactory;
        $this->resourceArticle = $resourceArticle;
        $this->resourceCategory = $resourceCategory;
        $this->config = $config;
        $this->pathUrlKeyExtractor = $pathUrlKeyExtractor;
        $this->urlKeyChecker = $urlKeyChecker;
        $this->redirectRequestToUrl = $redirectRequestToUrl;
    }

    /**
     * Match article page
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $pathInfo = (string)$request->getPathInfo();
        $path = explode('/', trim($pathInfo, '/'));
        $countPathParts = count($path);

        $assumeIsArticleWithCategoryPage = $countPathParts === 3;
        $assumeIsArticleWithoutCategoryPage = $countPathParts === 2
            && $path[1] !== Url::FAQ_SEARCH_ROUTE
            && $path[1] !== Url::FAQ_TAG_ROUTE;

        if ($assumeIsArticleWithCategoryPage || $assumeIsArticleWithoutCategoryPage) {
            if ($assumeIsArticleWithCategoryPage) {
                $categoryUrlKey = $path[1];
                $categoryId = $this->resourceCategory->getIdByUrlKey($categoryUrlKey);
                if ($categoryId) {
                    $request->setParam('category_id', $categoryId);
                } else {
                    return null;
                }
            }

            $articleUrlKeyWithSuffix = $this->pathUrlKeyExtractor->getUrlKey($pathInfo);
            $articleUrlSuffix = $this->config->getArticleUrlSuffix();

            if ($this->urlKeyChecker->isUrlKeyEndsWithIllegalSlash($articleUrlKeyWithSuffix, $articleUrlSuffix)) {
                return $this->redirectRequestToUrl->getPreparedRedirect($request, trim($pathInfo, '/'));
            }
            if ($this->urlKeyChecker->isUrlKeyRequiresSlashInEnd($articleUrlKeyWithSuffix, $articleUrlSuffix)) {
                return $this->redirectRequestToUrl->getPreparedRedirect($request, trim($pathInfo, '/') . '/');
            }

            $articleUrlKey = $this->pathUrlKeyExtractor->getUrlKeyWithoutSuffix($pathInfo, $articleUrlSuffix);
            if (!$articleUrlKey) {
                return null;
            }

            $articleId = $this->resourceArticle->getIdByUrlKey($articleUrlKey);
            if ($articleId) {
                $request
                    ->setModuleName('faq')
                    ->setControllerName('article')
                    ->setActionName('index')
                    ->setParam('id', $articleId);
            } else {
                return null;
            }
        } else {
            return null;
        }

        return $this->actionFactory->create(Forward::class, ['request' => $request]);
    }
}
