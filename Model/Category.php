<?php
namespace Aheadworks\FaqFree\Model;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\ResourceModel\Category as CategoryResource;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Category extends AbstractModel implements CategoryInterface, IdentityInterface
{
    /**
     * Category page cache tag
     */
    public const CACHE_TAG = 'faq_category';

    /**
     * Category constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CategoryResource::class);
    }

    /**
     * Get Category ID
     *
     * @return int|null
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * Returns identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getCategoryId()];
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get URL-key
     *
     * @return string
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * Get parent ID
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getData(self::PATH);
    }

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    /**
     * Get meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * Get sort order
     *
     * @return integer|null
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Get store view Ids
     *
     * @return int[]|null
     */
    public function getStoreIds()
    {
        $ids = $this->getData(self::STORE_IDS);

        if (empty($ids)) {
            return null;
        }

        return array_map('intval', (array)$ids);
    }

    /**
     * Get number of articles to display at FAQ Home Page
     *
     * @return integer|null
     */
    public function getNumArticlesToDisplay()
    {
        return $this->getData(self::NUM_ARTICLES_TO_DISPLAY);
    }

    /**
     * Get name of category icon
     *
     * @return string|null
     */
    public function getCategoryIcon()
    {
        return $this->getData(self::CATEGORY_ICON);
    }

    /**
     * Get an icon name for article listing
     *
     * @return string|null
     */
    public function getArticleListIcon()
    {
        return $this->getData(self::ARTICLE_LIST_ICON);
    }

    /**
     * Is enable
     *
     * @return bool
     */
    public function getIsEnable()
    {
        return (bool) $this->getData(self::IS_ENABLE);
    }

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Get creation time
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * Get is show children
     *
     * @return bool
     */
    public function getIsShowChildren()
    {
        return (bool)$this->getData(self::IS_SHOW_CHILDREN);
    }

    /**
     * Get is show children default
     *
     * @return bool
     */
    public function getIsShowChildrenDefault()
    {
        return (bool)$this->getData(self::IS_SHOW_CHILDREN_DEFAULT);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setCategoryId($id)
    {
        return $this->setData(self::CATEGORY_ID, $id);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set URL-key
     *
     * @param string $urlKey
     * @return $this
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * Set parent ID
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    /**
     * Set path
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        return $this->setData(self::PATH, $path);
    }

    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return $this
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return $this
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return $this
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Set store view Ids
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        return $this->setData(self::STORE_IDS, $storeIds);
    }

    /**
     * Set number of articles to display at FAQ Home Page
     *
     * @param integer $articlesNumber
     * @return $this
     */
    public function setNumArticlesToDisplay($articlesNumber)
    {
        return $this->setData(self::NUM_ARTICLES_TO_DISPLAY, $articlesNumber);
    }

    /**
     * Set name of category icon
     *
     * @param string $categoryIcon
     * @return $this
     */
    public function setCategoryIcon($categoryIcon)
    {
        return $this->setData(self::CATEGORY_ICON, $categoryIcon);
    }

    /**
     * Set an icon name for article listing
     *
     * @param string $articleListIcon
     * @return $this
     */
    public function setArticleListIcon($articleListIcon)
    {
        return $this->setData(self::ARTICLE_LIST_ICON, $articleListIcon);
    }

    /**
     * Set is enable
     *
     * @param bool $isEnable
     * @return $this
     */
    public function setIsEnable($isEnable)
    {
        return $this->setData(self::IS_ENABLE, $isEnable);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return $this
     */
    public function setCreatedAt($creationTime)
    {
        return $this->setData(self::CREATED_AT, $creationTime);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return $this
     */
    public function setUpdatedAt($updateTime)
    {
        return $this->setData(self::UPDATED_AT, $updateTime);
    }

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Set is show children
     *
     * @param bool $isShow
     * @return $this
     */
    public function setIsShowChildren($isShow)
    {
        return $this->setData(self::IS_SHOW_CHILDREN, $isShow);
    }

    /**
     * Set is show children default
     *
     * @param bool $isShowChildrenDefault
     * @return $this
     */
    public function setIsShowChildrenDefault($isShowChildrenDefault)
    {
        return $this->setData(self::IS_SHOW_CHILDREN_DEFAULT, $isShowChildrenDefault);
    }

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\FaqFree\Api\Data\CategoryExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\FaqFree\Api\Data\CategoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\FaqFree\Api\Data\CategoryExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
