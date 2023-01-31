<?php
namespace Aheadworks\FaqFree\Model\Metadata;

class FaqIndexPage extends AbstractMetadata
{
    /**
     * Get page entity title
     *
     * @param mixed|null $entity
     * @return string
     */
    protected function getPageEntityTitle($entity = null)
    {
        return $this->config->getFaqMetaTitle();
    }

    /**
     * Return meta description
     *
     * @param mixed|null $entity
     * @return string
     */
    public function getMetaDescription($entity = null)
    {
        return $this->config->getFaqMetaDescription();
    }

    /**
     * Return meta keywords
     *
     * @param mixed|null $entity
     * @return string
     */
    public function getMetaKeywords($entity = null)
    {
        return $this->config->getFaqMetaKeywords();
    }
}
