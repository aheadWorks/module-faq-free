<?php
namespace Aheadworks\FaqFree\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * FAQ category interface
 *
 * @api
 */
interface CategoryInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const CATEGORY_ID              = 'category_id';
    public const NAME                     = 'name';
    public const IS_ENABLE                = 'is_enable';
    public const URL_KEY                  = 'url_key';
    public const PARENT_ID                = 'parent_id';
    public const PATH                     = 'path';
    public const STORE_IDS                = 'store_ids';
    public const SORT_ORDER               = 'sort_order';
    public const NUM_ARTICLES_TO_DISPLAY  = 'num_articles_to_display';
    public const META_TITLE               = 'meta_title';
    public const META_KEYWORDS            = 'meta_keywords';
    public const META_DESCRIPTION         = 'meta_description';
    public const CATEGORY_ICON            = 'category_icon';
    public const ARTICLE_LIST_ICON        = 'article_list_icon';
    public const CREATED_AT               = 'created_at';
    public const UPDATED_AT               = 'update_at';
    public const CONTENT                  = 'content';
    public const IS_SHOW_CHILDREN         = 'is_show_children';
    public const IS_SHOW_CHILDREN_DEFAULT = 'is_show_children_default';
    public const SEARCH_STRING            = 'search_string';

    public const ROOT_CATEGORY_PARENT_ID = 0;
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getCategoryId();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get URL-key
     *
     * @return string
     */
    public function getUrlKey();

    /**
     * Get parent ID
     *
     * @return int
     */
    public function getParentId();

    /**
     * Get path
     *
     * @return string
     */
    public function getPath();

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Get meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Get sort order
     *
     * @return integer|null
     */
    public function getSortOrder();

    /**
     * Get store view Ids
     *
     * @return int[]|null
     */
    public function getStoreIds();

    /**
     * Get number of articles to display at FAQ Home Page
     *
     * @return integer|null
     */
    public function getNumArticlesToDisplay();

    /**
     * Get name of category icon
     *
     * @return string|null
     */
    public function getCategoryIcon();

    /**
     * Get an icon name for article listing
     *
     * @return string|null
     */
    public function getArticleListIcon();

    /**
     * Is enable
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsEnable();

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Get creation time
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Get is show children
     *
     * @return bool
     */
    public function getIsShowChildren();

    /**
     * Get is show children default
     *
     * @return bool
     */
    public function getIsShowChildrenDefault();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setCategoryId($id);

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Set URL-key
     *
     * @param string $urlKey
     * @return $this
     */
    public function setUrlKey($urlKey);

    /**
     * Set parent ID
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Set path
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path);

    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return $this
     */
    public function setMetaTitle($metaTitle);

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return $this
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Set store view Ids
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds);

    /**
     * Set number of articles to display at FAQ Home Page
     *
     * @param integer $articlesNumber
     * @return $this
     */
    public function setNumArticlesToDisplay($articlesNumber);

    /**
     * Set name of category icon
     *
     * @param string $categoryIcon
     * @return $this
     */
    public function setCategoryIcon($categoryIcon);

    /**
     * Set an icon name for article listing
     *
     * @param string $articleListIcon
     * @return $this
     */
    public function setArticleListIcon($articleListIcon);

    /**
     * Set is enable
     *
     * @param bool $isEnable
     * @return $this
     */
    public function setIsEnable($isEnable);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return $this
     */
    public function setCreatedAt($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return $this
     */
    public function setUpdatedAt($updateTime);

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Set is show children
     *
     * @param bool $isShow
     * @return $this
     */
    public function setIsShowChildren($isShow);

    /**
     * Set is show children default
     *
     * @param bool $isShowChildrenDefault
     * @return $this
     */
    public function setIsShowChildrenDefault($isShowChildrenDefault);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\FaqFree\Api\Data\CategoryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\FaqFree\Api\Data\CategoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\FaqFree\Api\Data\CategoryExtensionInterface $extensionAttributes
    );
}
