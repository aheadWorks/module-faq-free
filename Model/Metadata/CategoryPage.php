<?php
namespace Aheadworks\FaqFree\Model\Metadata;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Magento\Framework\Exception\LocalizedException;

class CategoryPage extends AbstractMetadata
{
    /**
     * Get page entity title
     *
     * @param mixed|null $entity
     * @return string
     */
    protected function getPageEntityTitle($entity = null)
    {
        $this->validateEntity($entity);
        return $entity->getMetaTitle() ?: $entity->getName();
    }

    /**
     * Return meta description
     *
     * @param mixed|null $entity
     * @return string
     */
    public function getMetaDescription($entity = null)
    {
        $this->validateEntity($entity);
        return $entity->getMetaDescription();
    }

    /**
     * Return meta keywords
     *
     * @param mixed|null $entity
     * @return string
     */
    public function getMetaKeywords($entity = null)
    {
        $this->validateEntity($entity);
        return $entity->getMetaKeywords();
    }

    /**
     * Validate entity
     *
     * @param mixed|null $entity
     * @throws LocalizedException
     */
    private function validateEntity($entity = null)
    {
        if (!$entity || !$entity instanceof CategoryInterface) {
            throw new LocalizedException(__('Entity must be instance of %1', CategoryInterface::class));
        }
    }
}
