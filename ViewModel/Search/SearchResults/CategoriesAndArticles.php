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
use Aheadworks\FaqFree\Model\Validator\SearchQuery\AbstractValidator as SearchQueryValidator;
use Aheadworks\FaqFree\Model\Validator\SearchQuery\SearchQueryDataObject;
use Aheadworks\FaqFree\Model\Validator\SearchQuery\SearchQueryDataObjectFactory;

class CategoriesAndArticles extends AbstractSearchResults
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
     * @var SearchQueryValidator
     */
    private $searchQueryValidator;

    /**
     * @var SearchQueryDataObjectFactory
     */
    private $searchQueryDataObjectFactory;

    /**
     * @param RequestInterface $request
     * @param Url $url
     * @param RedirectInterface $redirect
     * @param SearchManagementInterface $search
     * @param StoreManagerInterface $storeManager
     * @param SearchQueryValidator $searchQueryValidator
     * @param SearchQueryDataObjectFactory $searchQueryDataObjectFactory
     */
    public function __construct(
        RequestInterface $request,
        Url $url,
        RedirectInterface $redirect,
        SearchManagementInterface $search,
        StoreManagerInterface $storeManager,
        SearchQueryValidator $searchQueryValidator,
        SearchQueryDataObjectFactory $searchQueryDataObjectFactory
    ) {
        parent::__construct($request, $url, $redirect);

        $this->search = $search;
        $this->storeManager = $storeManager;
        $this->searchQueryValidator = $searchQueryValidator;
        $this->searchQueryDataObjectFactory = $searchQueryDataObjectFactory;
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
                $this->storeManager->getStore()->getId()
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
        try {
            $result =  $this->search->searchCategories(
                $this->getSearchQuery(),
                $this->storeManager->getStore()->getId()
            )->getItems();
        } catch (NoSuchEntityException $e) {
            $result = [];
        }
        return $result;
    }

    /**
     * Returns error message if
     *
     * @return string|null
     */
    public function getErrorMessage()
    {
        $message = '';

        try {
            /** @var SearchQueryDataObject $searchQueryDataObject */
            $searchQueryDataObject = $this->searchQueryDataObjectFactory
                ->create()
                ->setQueryString($this->getSearchQuery())
                ->setStoreId($this->storeManager->getStore()->getId());

            if (!$this->searchQueryValidator->isValid($searchQueryDataObject)) {
                $messages = $this->searchQueryValidator->getMessages();
                $message = array_shift($messages);
            }
        } catch (\Exception $e) {
            $message = '';
        }

        return $message;
    }
}
