<?php
namespace Aheadworks\FaqFree\ViewModel\Product;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface as CategoryRepository;
use Aheadworks\FaqFree\Model\Url;

class ArticleListItem implements ArgumentInterface
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var Url
     */
    private $url;

    /**
     * ArticleListItemRenderer constructor.
     * @param CategoryRepository $categoryRepository
     * @param Url $url
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        Url $url
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->url = $url;
    }

    /**
     * Returns article category
     *
     * @param ArticleInterface $article
     * @return CategoryInterface|null
     */
    public function getArticleCategory($article)
    {
        try {
            /** @var CategoryInterface $category */
            $category = $this->categoryRepository->getById($article->getCategoryId());
        } catch (\Exception $e) {
            $category = null;
        }

        return $category;
    }

    /**
     * Returns category url
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->url->getCategoryUrl($category);
    }

    /**
     * Returns article url
     *
     * @param ArticleInterface $article
     * @return string
     */
    public function getArticleUrl($article)
    {
        return $this->url->getArticleUrl($article);
    }
}
