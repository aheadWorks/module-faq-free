<?php
namespace Aheadworks\FaqFree\Model\Article\StructuredData\Provider;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\Article\StructuredData\ProviderInterface;

class Base implements ProviderInterface
{
    /**
     * Get prepared structured data array for the faq article
     *
     * @param ArticleInterface $article
     * @param int $storeId
     * @return array
     */
    public function getData(ArticleInterface $article, int $storeId)
    {
        return [
            "@context" => "https://schema.org/",
            "@type" => "Article",
            "headline" => $article->getTitle(),
        ];
    }
}
