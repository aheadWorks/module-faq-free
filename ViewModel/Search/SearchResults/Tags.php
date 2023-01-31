<?php
namespace Aheadworks\FaqFree\ViewModel\Search\SearchResults;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Api\SearchManagementInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\FaqFree\Model\Url;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Response\RedirectInterface;

class Tags extends AbstractSearchResults
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SearchManagementInterface
     */
    private $search;

    /**
     * Tags constructor.
     * @param RequestInterface $request
     * @param Url $url
     * @param RedirectInterface $redirect
     * @param SearchManagementInterface $search
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RequestInterface $request,
        Url $url,
        RedirectInterface $redirect,
        SearchManagementInterface $search,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($request, $url, $redirect);

        $this->search = $search;
        $this->storeManager = $storeManager;
    }

    /**
     * Returns found articles
     *
     * @returns ArticleInterface[]
     */
    public function getArticles()
    {
        try {
            $result = $this->search->searchArticles(
                $this->getSearchQuery(),
                $this->storeManager->getStore()->getId(),
                null,
                true
            )->getItems();
        } catch (NoSuchEntityException $e) {
            $result = [];
        }
        return $result;
    }

    /**
     * Returns found categories
     *
     * @returns CategoryInterface[]
     */
    public function getCategories()
    {
        return [];
    }
}
