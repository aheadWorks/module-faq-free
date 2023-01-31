<?php
namespace Aheadworks\FaqFree\Api\Data;

use Magento\Catalog\Api\Data\ProductAttributeInterface as CatalogProductAttributeInterface;

/**
 * Interface ProductAttributeInterface
 */
interface ProductAttributeInterface extends CatalogProductAttributeInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    public const CODE_AW_FAQ_ARTICLES = 'aw_faq_articles';
    /**#@-*/
}
