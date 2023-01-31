<?php
namespace Aheadworks\FaqFree\ViewModel\CategoryTree;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\CategoryTree as CategoryTreeModel;
use Aheadworks\FaqFree\Model\Url;
use Aheadworks\FaqFree\Model\Category\Manager as FaqCategoryManager;

class TreeFormatter
{
    private const CSS_CLASS_ACTIVE_NODE = 'aw-faq-sidebar-tree-active-node';
    private const CSS_CLASS_ACTIVE_NODE_ANCESTOR = 'aw-faq-sidebar-tree-active-node-ancestor';
    private const CSS_CLASS_TOP_LEVEL_NODE = 'aw-faq-sidebar-tree-top-level';

    private const CSS_ID_CATEGORY_NODE = 'aw-faq-category';
    private const CSS_ID_CATEGORY_NODE_EMPTY_PARENT = '#';
    private const CSS_ID_ARTICLE_NODE = 'aw-faq-article';

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var FaqCategoryManager
     */
    protected $faqCategoryManager;

    /**
     * @var CategoryInterface
     */
    protected $currentCategory;

    /**
     * @var ArticleInterface
     */
    protected $currentArticle;

    /**
     * @param Url $url
     * @param FaqCategoryManager $faqCategoryManager
     */
    public function __construct(
        Url $url,
        FaqCategoryManager $faqCategoryManager
    ) {
        $this->url = $url;
        $this->faqCategoryManager = $faqCategoryManager;
    }

    /**
     * Set current category
     *
     * @param CategoryInterface $category
     * @return $this
     */
    public function setCurrentCategory($category)
    {
        $this->currentCategory = $category;
        return $this;
    }

    /**
     * Set current article
     *
     * @param ArticleInterface $article
     * @return $this
     */
    public function setCurrentArticle($article)
    {
        $this->currentArticle = $article;
        return $this;
    }

    /**
     * Create category tree array of appropriate format
     *
     * @param CategoryTreeModel $categoryTree
     * @return array
     */
    public function format($categoryTree)
    {
        $result = [];
        $data = [];
        foreach ($categoryTree->getChildren() as $categoryTreeItem) {
            /** @var CategoryInterface $category */
            $category = $categoryTreeItem->getCategory();

            $result[] = [
                'id' => $this->getCssIdForCategory($category),
                'parent' => $this->getCssIdForCategoryParent($category),
                'text' => $this->formatItemName($category->getName()),
                'state' => [
                    'opened' => $this->isCategoryOpened($category)
                ],
                'li_attr' => [
                    'class' => $this->getCssClassesForCategory($category)
                ],
                'a_attr' => [
                    'href' => $this->url->getCategoryUrl($category),
                ],
            ];

            /** @var ArticleInterface $article */
            foreach ($categoryTreeItem->getArticles() as $article) {
                $result[] = [
                    'id' => $this->getCssIdForArticle($article),
                    'parent' => $this->getCssIdForCategory($category),
                    'text' => $this->formatItemName($article->getTitle()),
                    'li_attr' => [
                        'class' => $this->getCssClassesForArticle($article)
                    ],
                    'a_attr' => [
                        'href' => $this->url->getArticleUrl($article),
                    ],
                ];
            }

            //recursive call for child trees
            $data[] = $this->format($categoryTreeItem);
        }

        return array_merge($result, ...$data);
    }

    /**
     * Get formatted item name
     *
     * @param string $name
     * @return string
     */
    protected function formatItemName($name)
    {
        $name = str_replace("'", '&#39;', (string)$name);
        return $name;
    }

    /**
     * Create css id for category
     *
     * @param CategoryInterface $category
     * @return string
     */
    protected function getCssIdForCategory($category)
    {
        return self::CSS_ID_CATEGORY_NODE . $category->getCategoryId();
    }

    /**
     * Create css id for category parent node
     *
     * @param CategoryInterface $category
     * @return string
     */
    protected function getCssIdForCategoryParent($category)
    {
        return $category->getParentId() == CategoryInterface::ROOT_CATEGORY_PARENT_ID
            ? self::CSS_ID_CATEGORY_NODE_EMPTY_PARENT
            : self::CSS_ID_CATEGORY_NODE . $category->getParentId();
    }

    /**
     * Create css id for article
     *
     * @param ArticleInterface $article
     * @return string
     */
    protected function getCssIdForArticle($article)
    {
        return self::CSS_ID_ARTICLE_NODE . $article->getArticleId();
    }

    /**
     * Return css classes for category based on different conditions
     *
     * @param CategoryInterface $category
     * @return string
     */
    protected function getCssClassesForCategory($category)
    {
        $classes = [];

        if ($category->getParentId() == CategoryInterface::ROOT_CATEGORY_PARENT_ID) {
            $classes[] = self::CSS_CLASS_TOP_LEVEL_NODE;
        }

        if ($this->currentCategory
            && $this->currentCategory->getCategoryId() == $category->getCategoryId()) {
            $classes[] = self::CSS_CLASS_ACTIVE_NODE;
        }

        if ($this->currentCategory
            && $this->faqCategoryManager->isCategoryDescendantOfCategory($this->currentCategory, $category)) {
            $classes[] = self::CSS_CLASS_ACTIVE_NODE_ANCESTOR;
        }

        if ($this->currentArticle
            && $this->faqCategoryManager->isArticleDescendantOfCategory($this->currentArticle, $category)) {
            $classes[] = self::CSS_CLASS_ACTIVE_NODE_ANCESTOR;
        }

        return implode(' ', $classes);
    }

    /**
     * Return css classes for article based on different conditions
     *
     * @param ArticleInterface $article
     * @return string
     */
    protected function getCssClassesForArticle($article)
    {
        $classes = [];

        if ($this->currentArticle
            && $this->currentArticle->getArticleId() == $article->getArticleId()) {
            $classes[] = self::CSS_CLASS_ACTIVE_NODE;
        }

        return implode(' ', $classes);
    }

    /**
     * Check if category opened
     *
     * @param CategoryInterface $category
     * @return bool
     */
    protected function isCategoryOpened($category)
    {
        $result = false;

        if ($category->getParentId() == CategoryInterface::ROOT_CATEGORY_PARENT_ID) {
            $result = true;
        }

        if ($this->currentArticle
            && $this->faqCategoryManager->isArticleDescendantOfCategory($this->currentArticle, $category)) {
            $result = true;
        }

        if ($this->currentCategory
            && $this->faqCategoryManager->isCategoryDescendantOfCategory($this->currentCategory, $category)) {
            $result = true;
        }

        return $result;
    }
}
