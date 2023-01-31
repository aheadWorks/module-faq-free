<?php
namespace Aheadworks\FaqFree\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TagInterface
 * @api
 */
interface TagInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    public const ID = 'id';
    public const NAME = 'name';
    public const ARTICLE_ID = 'article_id';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get article id
     *
     * @return int
     */
    public function getArticleId();

    /**
     * Set article id
     *
     * @param int $articleId
     * @return $this
     */
    public function setArticleId($articleId);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\FaqFree\Api\Data\TagExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\FaqFree\Api\Data\TagExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aheadworks\FaqFree\Api\Data\TagExtensionInterface $extensionAttributes);
}
