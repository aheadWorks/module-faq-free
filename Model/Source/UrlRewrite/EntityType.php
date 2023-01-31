<?php
namespace Aheadworks\FaqFree\Model\Source\UrlRewrite;

class EntityType
{
    /**#@+
     * Entity types for url rewrites
     */
    public const TYPE_ARTICLE = 'aw-faq-article';
    public const TYPE_CATEGORY = 'aw-faq-category';
    public const TYPE_FAQ_ROUTE = 'aw-faq-route';
    /**#@-*/

    /**
     * Get entities
     *
     * @return array
     */
    public function getEntityArray()
    {
        return [
            self::TYPE_ARTICLE,
            self::TYPE_CATEGORY
        ];
    }
}
