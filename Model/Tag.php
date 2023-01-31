<?php
namespace Aheadworks\FaqFree\Model;

use Aheadworks\FaqFree\Api\Data\TagInterface;
use Aheadworks\FaqFree\Model\ResourceModel\Tag as ResourceTag;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\FaqFree\Api\Data\TagExtensionInterface;

class Tag extends AbstractModel implements TagInterface
{
    /**
     * Tag constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceTag::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
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
     * Get article id
     *
     * @return int
     */
    public function getArticleId()
    {
        return $this->getData(self::ARTICLE_ID);
    }

    /**
     * Set article id
     *
     * @param int $articleId
     * @return $this
     */
    public function setArticleId($articleId)
    {
        return $this->setData(self::ARTICLE_ID, $articleId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return TagExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object
     *
     * @param TagExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(TagExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
