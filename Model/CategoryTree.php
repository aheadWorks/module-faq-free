<?php
namespace Aheadworks\FaqFree\Model;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Magento\Framework\DataObject;

/**
 * @method $this setCategory(CategoryInterface $category)
 * @method $this setArticles(ArticleInterface[] $articles)
 * @method $this setChildren(CategoryTree[] $children)
 * @method CategoryInterface getCategory()
 * @method ArticleInterface[] getArticles()
 * @method CategoryTree[] getChildren()
 */
class CategoryTree extends DataObject
{
}
