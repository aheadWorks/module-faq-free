<?php
namespace Aheadworks\FaqFree\ViewModel\Article;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\TagInterface;
use Aheadworks\FaqFree\Api\TagRepositoryInterface;
use Aheadworks\FaqFree\Model\Url;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Tags implements ArgumentInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var Url
     */
    private $url;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param TagRepositoryInterface $tagRepository
     * @param Url $url
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        TagRepositoryInterface $tagRepository,
        Url $url
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->request = $request;
        $this->tagRepository = $tagRepository;
        $this->url = $url;
    }

    /**
     * Get items
     *
     * @param int $articleId
     * @return TagInterface[]
     */
    public function getItems($articleId)
    {
        $this->searchCriteriaBuilder->addFilter(ArticleInterface::ARTICLE_ID, $articleId);
        try {
            $tags = $this->tagRepository->getList(
                $this->searchCriteriaBuilder->create()
            )->getItems();
        } catch (LocalizedException $exception) {
            $tags = [];
        }

        return $tags;
    }

    /**
     * Get search url by tag
     *
     * @param TagInterface|string $tag
     * @return string
     */
    public function getSearchUrlByTag($tag)
    {
        return $this->url->getSearchUrlByTag($tag);
    }
}
