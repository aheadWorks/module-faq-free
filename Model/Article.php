<?php
namespace Aheadworks\FaqFree\Model;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\FaqFree\Model\ResourceModel\Article as ArticleResource;

class Article extends AbstractModel implements ArticleInterface, IdentityInterface
{
    /**
     * FAQ article cache tag
     */
    public const CACHE_TAG = 'faq_article';

    /**
     * Article constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ArticleResource::class);
    }

    /**
     * Get article ID
     *
     * @return int|null
     */
    public function getArticleId()
    {
        return $this->getData(self::ARTICLE_ID);
    }

    /**
     * Returns identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getArticleId()];
    }

    /**
     * Get category Id
     *
     * @return int|null
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * Get number of views
     *
     * @return integer|null
     */
    public function getViewCount()
    {
        return $this->getData(self::VIEWS_COUNT);
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
     * Is enable
     *
     * @return bool
     */
    public function getIsEnable()
    {
        return (bool)$this->getData(self::IS_ENABLE);
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
     * Get update time
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
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
     * Get content
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
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
     * Get tag ids
     *
     * @return int[]|null
     */
    public function getTagIds()
    {
        return $this->getData(self::TAG_IDS);
    }

    /**
     * Get tag names
     *
     * @return string[]|null
     */
    public function getTagNames()
    {
        return $this->getData(self::TAG_NAMES);
    }

    /**
     * Get sort order
     *
     * @return int|null
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setArticleId($id)
    {
        return $this->setData(self::ARTICLE_ID, $id);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
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
     * Set tag ids
     *
     * @param int[] $tagIds
     * @return $this
     */
    public function setTagIds($tagIds)
    {
        return $this->setData(self::TAG_IDS, $tagIds);
    }

    /**
     * Set tag names
     *
     * @param string[] $tagNames
     * @return $this
     */
    public function setTagNames($tagNames)
    {
        return $this->setData(self::TAG_NAMES, $tagNames);
    }

    /**
     * Set sort order
     *
     * @param integer $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
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
     * Set number of views
     *
     * @param integer $viewCount
     * @return $this
     */
    public function setViewsCount($viewCount)
    {
        return $this->setData(self::VIEWS_COUNT, $viewCount);
    }

    /**
     * Set category Id
     *
     * @param int $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }
}
