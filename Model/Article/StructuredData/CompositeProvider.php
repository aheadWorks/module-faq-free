<?php
namespace Aheadworks\FaqFree\Model\Article\StructuredData;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;

class CompositeProvider implements ProviderInterface
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * Get prepared structured data array for the faq article
     *
     * @param ArticleInterface $article
     * @param int $storeId
     * @return array
     */
    public function getData(ArticleInterface $article, int $storeId)
    {
        $data = [];
        foreach ($this->providers as $provider) {
            if ($provider instanceof ProviderInterface) {
                $data[] = $provider->getData($article, $storeId);
            }
        }

        return array_merge([], ...$data);
    }
}
