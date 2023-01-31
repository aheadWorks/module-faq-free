<?php
namespace Aheadworks\FaqFree\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ArticleInterface
 */
interface ArticleInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ARTICLE_ID               = 'article_id';
    public const TITLE                    = 'title';
    public const IS_ENABLE                = 'is_enable';
    public const URL_KEY                  = 'url_key';
    public const STORE_IDS                = 'store_ids';
    public const SORT_ORDER               = 'sort_order';
    public const CONTENT                  = 'content';
    public const META_TITLE               = 'meta_title';
    public const META_KEYWORDS            = 'meta_keywords';
    public const META_DESCRIPTION         = 'meta_description';
    public const TAG_IDS                  = 'tag_ids';
    public const TAG_NAMES                = 'tag_names';
    public const CREATED_AT               = 'created_at';
    public const UPDATED_AT               = 'updated_at';
    public const VIEWS_COUNT              = 'views_count';
    public const CATEGORY_ID              = 'category_id';
    public const PRODUCT_IDS              = 'product_ids';
    public const IS_CATEGORY_ENABLE       = 'is_category_enable';
    public const SEARCH_STRING            = 'search_string';
    //todo: now only product ids filtering works,
    //it would be good to add info about products to which articles attached including all processing(e.g. via web api)
    /**#@-*/

    /**
     * Get article ID
     *
     * @return int|null
     */
    public function getArticleId();

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get URL-key
     *
     * @return string
     */
    public function getUrlKey();

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
     * Get tag ids
     *
     * @return int[]|null
     */
    public function getTagIds();

    /**
     * Get tag names
     *
     * @return string[]|null
     */
    public function getTagNames();

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Get sort order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Get store view Ids
     *
     * @return int[]|null
     */
    public function getStoreIds();

    /**
     * Get category Id
     *
     * @return int|null
     */
    public function getCategoryId();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Get number of views
     *
     * @return integer|null
     */
    public function getViewCount();

    /**
     * Is enable
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsEnable();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setArticleId($id);

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Set URL-key
     *
     * @param string $urlKey
     * @return $this
     */
    public function setUrlKey($urlKey);

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
     * Set tag ids
     *
     * @param int[] $tagIds
     * @return $this
     */
    public function setTagIds($tagIds);

    /**
     * Set tag names
     *
     * @param string[] $tagNames
     * @return $this
     */
    public function setTagNames($tagNames);

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
     * Set sort order
     *
     * @param integer $sortOrder
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
     * Set category Id
     *
     * @param int $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId);

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Set number of views
     *
     * @param integer $viewCount
     * @return $this
     */
    public function setViewsCount($viewCount);

    /**
     * Set is enable
     *
     * @param bool $isEnable
     * @return $this
     */
    public function setIsEnable($isEnable);
}
