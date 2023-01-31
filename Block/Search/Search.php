<?php
namespace Aheadworks\FaqFree\Block\Search;

use Aheadworks\FaqFree\Model\Url;
use Aheadworks\FaqFree\Block\AbstractTemplate;

class Search extends AbstractTemplate
{
    /**
     * Retrieve search query string
     *
     * @return string
     */
    public function getSearchQueryString()
    {
        return $this->getRequest()->getParam($this->getQueryParamName());
    }

    /**
     * Retrieve FAQ Search query parameter name
     *
     * @return string
     */
    public function getQueryParamName()
    {
        return Url::FAQ_QUERY_PARAM;
    }

    /**
     * Retrieve route name where request came from
     *
     * @return string
     */
    public function getRouteName()
    {
        return substr($this->getRequest()->getPathInfo(), 1);
    }
}
