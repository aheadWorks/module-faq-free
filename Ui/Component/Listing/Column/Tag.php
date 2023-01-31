<?php
namespace Aheadworks\FaqFree\Ui\Component\Listing\Column;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Tag extends Column
{
    /**
     * Consolidate all tags in one field
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $post) {
                if (is_array($post[ArticleInterface::TAG_NAMES])) {
                    $post['tags'] = implode(', ', $post[ArticleInterface::TAG_NAMES]);
                }
            }
        }
        return $dataSource;
    }
}
