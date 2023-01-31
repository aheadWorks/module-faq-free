<?php
namespace Aheadworks\FaqFree\Model\Metadata;

interface MetadataInterface
{
    /**
     * Return title
     *
     * @param mixed|null $entity
     * @return string
     */
    public function getTitle($entity = null);

    /**
     * Return meta title
     *
     * @param mixed|null $entity
     * @return string
     */
    public function getMetaTitle($entity = null);

    /**
     * Return meta description
     *
     * @param mixed|null $entity
     * @return string
     */
    public function getMetaDescription($entity = null);

    /**
     * Return meta keywords
     *
     * @param mixed|null $entity
     * @return string
     */
    public function getMetaKeywords($entity = null);
}
