<?php
namespace Aheadworks\FaqFree\Model\Config;

use Aheadworks\FaqFree\Model\Config\Backend\Route;
use Magento\Framework\DataObject;

class FaqRoute
{
    /**
     * Path to current FAQ route
     */
    private const FAQ_ROUTE_PATH = 'groups/general/fields/faq_route';

    /**
     * Retrieve current Faq route
     *
     * @param array $data
     * @return string
     */
    public function getCurrentFaqRoute($data)
    {
        $faqUrlPathParts = explode('/', self::FAQ_ROUTE_PATH);
        $faqUrlData = $this->getDataByPathParts($data, $faqUrlPathParts);

        return !empty($faqUrlData['inherit']) ? Route::DEFAULT_FAQ_ROUTE : $faqUrlData['value'];
    }

    /**
     * Walk nested hash map by keys from $pathParts.
     *
     * @param array $data to walk in
     * @param array $pathParts keys path
     * @return mixed
     */
    private function getDataByPathParts($data, $pathParts)
    {
        foreach ($pathParts as $key) {
            if ((array)$data === $data && isset($data[$key])) {
                $data = $data[$key];
            } elseif ($data instanceof DataObject) {
                $data = $data->getDataByKey($key);
            } else {
                return null;
            }
        }

        return $data;
    }
}
