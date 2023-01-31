<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Processor\Save\Entity;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\UrlRewrites\Service\Category as CategoryRewritesService;
use Magento\Framework\Exception\LocalizedException;

class Category
{
    /**
     * @var CategoryRewritesService
     */
    private $categoryRewritesService;

    /**
     * Category constructor.
     * @param CategoryRewritesService $categoryRewritesService
     */
    public function __construct(
        CategoryRewritesService $categoryRewritesService
    ) {
        $this->categoryRewritesService = $categoryRewritesService;
    }

    /**
     * Process rewrites based on new and orig category state
     *
     * @param CategoryInterface $category
     * @param CategoryInterface|null $origCategory
     * @return bool
     * @throws LocalizedException
     */
    public function process($category, $origCategory = null)
    {
        if ($category && $origCategory) {
            $this->categoryRewritesService->updatePermanentRedirects($category, $origCategory);
        }

        $this->categoryRewritesService->updateControllerRewrites($category);

        return true;
    }
}
