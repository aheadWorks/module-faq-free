<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface as CategoryRepository;
use Magento\Framework\Exception\LocalizedException;

class PathBuilder
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * PathBuilder constructor.
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Builds faq path
     *
     * @param UrlConfigMetadataModel $urlConfigMetadata
     * @return string
     */
    public function buildFaqHomePath($urlConfigMetadata)
    {
        return trim((string)$urlConfigMetadata->getFaqRoute(), '/');
    }

    /**
     * Builds category path
     *
     * @param UrlConfigMetadataModel $urlConfigMetadata
     * @param CategoryInterface $category
     * @return string
     */
    public function buildCategoryPath($urlConfigMetadata, $category)
    {
        $path =  $this->buildFaqHomePath($urlConfigMetadata)
            . '/' . $category->getUrlKey()
            . $urlConfigMetadata->getCategoryUrlSuffix();

        return trim($path, '/');
    }

    /**
     * Builds article path
     *
     * @param UrlConfigMetadataModel $urlConfigMetadata
     * @param ArticleInterface $article
     * @param CategoryInterface|null $category
     * @throws LocalizedException
     * @return string
     */
    public function buildArticlePath($urlConfigMetadata, $article, $category = null)
    {
        if (!$category) {
            $category = $this->categoryRepository->getById($article->getCategoryId());
        }

        $path = $this->buildFaqHomePath($urlConfigMetadata)
            . '/' . $category->getUrlKey()
            . '/' . $article->getUrlKey()
            . $urlConfigMetadata->getArticleUrlSuffix();

        return trim($path, '/');
    }
}
