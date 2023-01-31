<?php
namespace Aheadworks\FaqFree\Model\SearchQueryFormatter;

class Mysql implements FormatterInterface
{
    /**
     * Prepare Search Query
     *
     * @param string $searchQuery
     * @return string
     */
    public function format($searchQuery)
    {
        $query = preg_split('/[\s*\W*]/', strip_tags((string)$searchQuery));
        $searchWords = [];

        foreach ($query as $word) {
            if (mb_strlen($word) > 2) {
                $searchWords[] = trim($word) . '*';
            }
        }

        return implode(' ', $searchWords);
    }
}
