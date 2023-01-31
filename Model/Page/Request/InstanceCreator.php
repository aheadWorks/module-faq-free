<?php
namespace Aheadworks\FaqFree\Model\Page\Request;

use Magento\Framework\App\RequestInterface;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;

class InstanceCreator
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * InstanceCreator constructor.
     * @param RequestInterface $request
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ArticleRepositoryInterface $articleRepository
     */
    public function __construct(
        RequestInterface $request,
        CategoryRepositoryInterface $categoryRepository,
        ArticleRepositoryInterface $articleRepository
    ) {
        $this->request = $request;
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Retrieve current category instance
     *
     * @return CategoryInterface|null
     */
    public function getCurrentCategory()
    {
        $categoryId = $this->request->getParam('id');

        try {
            $category = $this->categoryRepository->getById($categoryId);
        } catch (\Exception $e) {
            $category = null;
        }

        return $category;
    }

    /**
     * Retrieve current article instance
     *
     * @return ArticleInterface|null
     */
    public function getCurrentArticle()
    {
        $articleId = $this->request->getParam('id');

        try {
            $article = $this->articleRepository->getById($articleId);
        } catch (\Exception $e) {
            $article = null;
        }

        return $article;
    }
}
