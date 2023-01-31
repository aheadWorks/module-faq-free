<?php
namespace Aheadworks\FaqFree\Model\Article\StructuredData\Provider;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\Article\StructuredData\ProviderInterface;
use Aheadworks\FaqFree\Model\DateTime\Formatter as DateTimeFormatter;

class Dates implements ProviderInterface
{
    /**
     * @var DateTimeFormatter
     */
    private $dateTimeFormatter;

    /**
     * Dates constructor.
     * @param DateTimeFormatter $dateTimeFormatter
     */
    public function __construct(
        DateTimeFormatter $dateTimeFormatter
    ) {
        $this->dateTimeFormatter = $dateTimeFormatter;
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

        $datePublished = $article->getCreatedAt();
        if (!empty($datePublished)) {
            $data["datePublished"] = $this->getDateInIsoFormat($datePublished, $storeId);
        }

        $dateModified = $article->getUpdatedAt();
        if (!empty($dateModified)) {
            $data["dateModified"] = $this->getDateInIsoFormat($dateModified, $storeId);
        }

        return $data;
    }

    /**
     * Retrieve date in the ISO 8601 format
     *
     * @param string $date
     * @param int $storeId
     * @return string
     */
    protected function getDateInIsoFormat($date, $storeId)
    {
        $dateInIsoFormat = $this->dateTimeFormatter->getLocalizedDateTime(
            $date,
            $storeId,
            \DateTime::ISO8601
        );
        return $dateInIsoFormat;
    }
}
