<?php
namespace Aheadworks\FaqFree\Model\Source\Product\Attribute\Articles;

use Aheadworks\FaqFree\Model\Source\Product\Attribute\Articles;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Eav\Model\Entity\Attribute\Backend\BackendInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

class Proxy extends AbstractBackend
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var BackendInterface|AbstractBackend
     */
    private $instance;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Manager $moduleManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Manager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Get source model instance
     *
     * @return AbstractSource
     */
    private function getInstance()
    {
        if (!$this->instance) {
            $className = $this->moduleManager->isEnabled('Aheadworks_FaqFree')
                ? Articles::class
                : Dummy::class;
            $this->instance = $this->objectManager->create($className);
        }
        return $this->instance;
    }

    /**
     * Set attribute instance
     *
     * @param AbstractAttribute $attribute
     * @return $this
     */
    public function setAttribute($attribute)
    {
        return $this->getInstance()->setAttribute($attribute);
    }

    /**
     * Get attribute instance
     *
     * @return AbstractAttribute
     */
    public function getAttribute()
    {
        return $this->getInstance()->getAttribute();
    }

    /**
     * Get backend type of the attribute
     *
     * @return string
     */
    public function getType()
    {
        return $this->getInstance()->getType();
    }

    /**
     * Check whether the attribute is a real field in the entity table
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isStatic()
    {
        return $this->getInstance()->isStatic();
    }

    /**
     * Get table name for the values of the attribute
     *
     * @return string
     */
    public function getTable()
    {
        return $this->getInstance()->getTable();
    }

    /**
     * Get entity_id field in the attribute values tables
     *
     * @return string
     */
    public function getEntityIdField()
    {
        return $this->getInstance()->getEntityIdField();
    }

    /**
     * Set value id
     *
     * @param int $valueId
     * @return $this
     */
    public function setValueId($valueId)
    {
        return $this->getInstance()->setValueId($valueId);
    }

    /**
     * Set entity value id
     *
     * @param \Magento\Framework\DataObject $entity
     * @param int $valueId
     * @return $this
     */
    public function setEntityValueId($entity, $valueId)
    {
        return $this->getInstance()
            ->setEntityValueId($entity, $valueId);
    }

    /**
     * Retrieve value id
     *
     * @return int
     */
    public function getValueId()
    {
        return $this->getInstance()->getValueId();
    }

    /**
     * Get entity value id
     *
     * @param \Magento\Framework\DataObject $entity
     * @return int
     */
    public function getEntityValueId($entity)
    {
        return $this->getInstance()->getEntityValueId($entity);
    }

    /**
     * Retrieve default value
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->getInstance()->getDefaultValue();
    }

    /**
     * Validate object
     *
     * @param \Magento\Framework\DataObject $object
     * @return bool
     */
    public function validate($object)
    {
        return $this->getInstance()->validate($object);
    }

    /**
     * After save method
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function afterSave($object)
    {
        return $this->getInstance()->afterSave($object);
    }

    /**
     * Before save method
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function beforeSave($object)
    {
        return $this->getInstance()->beforeSave($object);
    }

    /**
     * After load method
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function afterLoad($object)
    {
        return $this->getInstance()->afterLoad($object);
    }

    /**
     * Before delete method
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function beforeDelete($object)
    {
        return $this->getInstance()->beforeDelete($object);
    }

    /**
     * After delete method
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function afterDelete($object)
    {
        return $this->getInstance()->afterDelete($object);
    }

    /**
     * Retrieve data for update attribute
     *
     * @param \Magento\Framework\DataObject $object
     * @return array
     */
    public function getAffectedFields($object)
    {
        return $this->getInstance()->getAffectedFields($object);
    }

    /**
     * By default attribute value is considered scalar that can be stored in a generic way
     *
     * @codeCoverageIgnore
     */
    public function isScalar()
    {
        return $this->getInstance()->isScalar();
    }
}
