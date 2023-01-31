<?php
namespace Aheadworks\FaqFree\Model\DateTime;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

class Formatter
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        TimezoneInterface $localeDate
    ) {
        $this->localeDate = $localeDate;
    }

    /**
     * Retrieve formatted date and time, localized according to the specific store
     *
     * @param string|null $date
     * @param int|null $storeId
     * @param string $format
     * @return string
     */
    public function getLocalizedDateTime($date = null, $storeId = null, $format = StdlibDateTime::DATETIME_PHP_FORMAT)
    {
        $scopeDate = $this->localeDate->scopeDate($storeId, $date, true);
        return $scopeDate->format($format);
    }
}
