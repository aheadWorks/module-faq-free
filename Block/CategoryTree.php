<?php
namespace Aheadworks\FaqFree\Block;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\DataObject\IdentityInterface;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface as CategoryRepository;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface as ArticleRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\FaqFree\Model\Category as CategoryModel;
use Aheadworks\FaqFree\Model\Article as ArticleModel;

class CategoryTree extends Template implements IdentityInterface
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param CategoryRepository $categoryRepository
     * @param ArticleRepository $articleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        CategoryRepository $categoryRepository,
        ArticleRepository $articleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Returns identities
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];

        try {
            $categorySearchCriteria = $this->searchCriteriaBuilder->create();
            /** @var CategoryInterface[] $categories */
            $categories = $this->categoryRepository->getList($categorySearchCriteria)->getItems();
        } catch (LocalizedException $e) {
            $categories = [];
        }

        try {
            $articleSearchCriteria = $this->searchCriteriaBuilder->create();
            /** @var ArticleInterface[] $articles */
            $articles = $this->articleRepository->getList($articleSearchCriteria)->getItems();
        } catch (LocalizedException $e) {
            $articles = [];
        }

        foreach ($categories as $category) {
            $identities[] = CategoryModel::CACHE_TAG . '_' . $category->getCategoryId();
        }

        foreach ($articles as $article) {
            $identities[] = ArticleModel::CACHE_TAG . '_' . $article->getArticleId();
        }

        return $identities;
    }
}
