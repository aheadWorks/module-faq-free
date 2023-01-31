<?php

namespace Aheadworks\FaqFree\Model;

use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;
use Magento\Store\Model\Store;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * FAQ config model
 */
class Config
{
    /**
     * Default number of columns to display config path
     */
    private const XML_PATH_NUMBER_OF_COLUMNS = 'faq/general/number_of_columns';

    /**
     * Default number of columns to display config path
     */
    private const XML_PATH_FAQ_SEARCH_ENABLED = 'faq/general/faq_search_enabled';

    /**
     * Search query min length
     */
    private const XML_PATH_FAQ_MIN_SEARCH_QUERY_LENGTH = 'faq/general/faq_min_search_query_length';

    /**
     * Search query max length
     */
    private const XML_PATH_FAQ_MAX_SEARCH_QUERY_LENGTH = 'faq/general/faq_max_search_query_length';

    /**
     * Display link to FAQ in the main navigation config path
     */
    private const XML_PATH_NAVIGATION_MENU_LINK_ENABLED = 'faq/general/navigation_menu_link_enabled';

    /**
     * FAQ name path
     */
    private const XML_PATH_FAQ_NAME = 'faq/general/faq_name';

    /**
     * Customer groups who have not access to FAQ
     */
    private const XML_PATH_FAQ_GROUPS = 'faq/general/groups_with_disabled_faq';

    /**
     * FAQ route config path
     */
    private const XML_PATH_FAQ_ROUTE = 'faq/general/faq_route';

    /**
     * FAQ enable show category children
     */
    private const XML_PATH_FAQ_ENABLE_SHOW_CATEGORY_CHILDREN = 'faq/general/enable_show_category_children';

    /**
     * FAQ meta title config path
     */
    private const XML_PATH_FAQ_META_TITLE = 'faq/general/meta_title';

    /**
     * FAQ meta keywords config path
     */
    private const XML_PATH_FAQ_META_KEYWORDS = 'faq/general/meta_keywords';

    /**
     * FAQ meta description config path
     */
    private const XML_PATH_FAQ_META_DESCRIPTION = 'faq/general/meta_description';

    /**
     * Configuration path to faq sitemap change frequency
     */
    private const XML_PATH_SITEMAP_CHANGEFREQ = 'sitemap/aw_faq/changefreq';

    /**
     * Configuration path to faq sitemap priority
     */
    private const XML_PATH_SITEMAP_PRIORITY = 'sitemap/aw_faq/priority';

    /**
     * Default article url suffix
     */
    private const XML_PATH_SEO_ARTICLE_URL_SUFFIX = 'faq/seo/article_url_suffix';

    /**
     * Default category url suffix
     */
    private const XML_PATH_SEO_CATEGORY_URL_SUFFIX = 'faq/seo/category_url_suffix';

    /**
     * Configuration path to save rewrites history
     */
    private const XML_PATH_SEO_SAVE_REWRITES_HISTORY = 'faq/seo/save_rewrites_history';

    /**
     * Configuration path to category canonical tag
     */
    private const XML_PATH_SEO_CATEGORY_CANONICAL_TAG = 'faq/seo/category_canonical_tag';

    /**
     * Configuration path to article canonical tag
     */
    private const XML_PATH_SEO_ARTICLE_CANONICAL_TAG = 'faq/seo/article_canonical_tag';

    /**
     * Configuration path to title separator
     */
    private const XML_PATH_SEO_TITLE_SEPARATOR = 'faq/seo/title_separator';

    /**
     * Configuration path to title prefix
     */
    private const XML_PATH_SEO_TITLE_PREFIX = 'faq/seo/title_prefix';

    /**
     * Configuration path to title suffix
     */
    private const XML_PATH_SEO_TITLE_SUFFIX = 'faq/seo/title_suffix';

    /**
     * Configuration path to meta title suffix
     */
    private const XML_PATH_SEO_META_TITLE_SUFFIX = 'faq/seo/meta_title_suffix';

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Customer session
     *
     * @var Session
     */
    private $session;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $session
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Session $session,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->session = $session;
        $this->storeManager = $storeManager;
    }

    /**
     * Get faq display column count on FAQ homepage
     *
     * @param null|string|bool|int|Store $store
     * @return int
     */
    public function getDefaultNumberOfColumnsToDisplay($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NUMBER_OF_COLUMNS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get faq Storefront Name
     *
     * @return string
     */
    public function getFaqName()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_NAME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get faq route
     *
     * @param int $storeId
     * @return string
     * @internal param StoreManagerInterface $store
     * @internal param WebsiteInterface $website
     * @internal param StoreInterface $store
     */
    public function getFaqRoute($storeId = null)
    {
        $websiteCode = null;

        if ($storeId) {
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
            $websiteCode = $this->storeManager->getWebsite($websiteId)->getCode();
        }

        return $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_ROUTE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteCode
        );
    }

    /**
     * Get faq Meta title
     *
     * @return string
     */
    public function getFaqMetaTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_META_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get faq Meta keywords
     *
     * @return string
     */
    public function getFaqMetaKeywords()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_META_KEYWORDS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get faq Meta description
     *
     * @return string
     */
    public function getFaqMetaDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_META_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks if FAQ search is enabled
     *
     * @return bool
     */
    public function isFaqSearchEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_FAQ_SEARCH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks if FAQ link in Categories is enabled
     *
     * @return bool
     */
    public function isNavigationMenuLinkEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NAVIGATION_MENU_LINK_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get customer groups who can view FAQ content
     *
     * @return array|bool
     */
    public function getFaqGroups()
    {
        $settingsValue = $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_GROUPS,
            ScopeInterface::SCOPE_STORE
        );

        return $settingsValue !== null ? explode(',', (string)$settingsValue) : false;
    }

    /**
     * Check disabling FAQ for current user
     *
     * @return bool
     */
    public function isDisabledFaqForCurrentCustomer()
    {
        $groups = $this->getFaqGroups();
        $groupId = (string)$this->session->getCustomerGroupId();
        if (!$groups) {
            return false;
        }
        return !(in_array($groupId, $groups) || in_array(Group::CUST_GROUP_ALL, $groups));
    }

    /**
     * Get faq change frequency
     *
     * @param int $storeId
     * @return string
     */
    public function getSitemapChangeFrequency($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEMAP_CHANGEFREQ,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get min search query length
     *
     * @param int $storeId
     * @return int
     */
    public function getMinSearchQueryLength($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_FAQ_MIN_SEARCH_QUERY_LENGTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get max search query length
     *
     * @param int $storeId
     * @return int
     */
    public function getMaxSearchQueryLength($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_FAQ_MAX_SEARCH_QUERY_LENGTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get faq priority
     *
     * @param int $storeId
     * @return string
     */
    public function getSitemapPriority($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEMAP_PRIORITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get is enabled show category children
     *
     * @return bool
     */
    public function getIsShowCategoryChildren()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_FAQ_ENABLE_SHOW_CATEGORY_CHILDREN,
            ScopeInterface::SCOPE_WEBSITES
        );
    }

    /**
     * Get seo article url suffix
     *
     * @param int|null $storeId
     * @return string
     */
    public function getArticleUrlSuffix($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SEO_ARTICLE_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo category url suffix
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCategoryUrlSuffix($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SEO_CATEGORY_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo save rewrites history
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSaveRewritesHistory($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_SAVE_REWRITES_HISTORY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo title separator
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTitleSeparator($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_TITLE_SEPARATOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo category canonical tag
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCategoryCanonicalTag($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_CATEGORY_CANONICAL_TAG,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo article canonical tag
     *
     * @param int|null $storeId
     * @return string
     */
    public function getArticleCanonicalTag($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_ARTICLE_CANONICAL_TAG,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo title prefix
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTitlePrefix($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_TITLE_PREFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo title suffix
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTitleSuffix($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_TITLE_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo meta title suffix
     *
     * @param int|null $storeId
     * @return string
     */
    public function getMetaTitleSuffix($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_META_TITLE_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
