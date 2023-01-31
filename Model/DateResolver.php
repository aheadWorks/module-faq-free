<?php
namespace Aheadworks\FaqFree\Model;

use Magento\Framework\Stdlib\DateTime\DateTime;

class DateResolver
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param DateTime $dateTime
     */
    public function __construct(
        DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
    }

    /**
     * Get current datetime in DB format
     *
     * @return string
     */
    public function getCurrentDatetimeInDbFormat()
    {
        return $this->dateTime->gmtDate();
    }

    /**
     * Get current datetime with offset in seconds
     *
     * @param int $offset
     * @param string $format
     * @return string
     */
    public function getCurrentDatetimeWithOffset($offset, $format = 'Y-m-d H:i:s')
    {
        $date = $this->dateTime->gmtTimestamp();
        $date += $offset;

        $result = date($format, $date);
        return $result;
    }
}
