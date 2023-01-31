<?php
namespace Aheadworks\FaqFree\Model\Article\StructuredData;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;

interface ProviderInterface
{
    /**
     * Get prepared structured data array for the faq article
     *
     * @param ArticleInterface $article
     * @param int $storeId
     * @return mixed
     */
    public function getData(ArticleInterface $article, int $storeId);
}
