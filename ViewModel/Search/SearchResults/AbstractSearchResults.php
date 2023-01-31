<?php
namespace Aheadworks\FaqFree\ViewModel\Search\SearchResults;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\FaqFree\Model\Url;
use Magento\Framework\App\Response\RedirectInterface;

abstract class AbstractSearchResults implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * AbstractSearchResults constructor.
     * @param RequestInterface $request
     * @param Url $url
     * @param RedirectInterface $redirect
     */
    public function __construct(
        RequestInterface $request,
        Url $url,
        RedirectInterface $redirect
    ) {
        $this->request = $request;
        $this->url = $url;
        $this->redirect = $redirect;
    }

    /**
     * Returns found articles
     *
     * @returns ArticleInterface[]
     */
    abstract public function getArticles();

    /**
     * Returns found categories
     *
     * @returns CategoryInterface[]
     */
    abstract public function getCategories();

    /**
     * Retrieve search query
     *
     * @return string
     */
    public function getSearchQuery()
    {
        return $this->request->getParam(Url::FAQ_QUERY_PARAM);
    }

    /**
     * Retrieve category url
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getCategoryUrl(CategoryInterface $category)
    {
        return $this->url->getCategoryUrl($category);
    }

    /**
     * Retrieve article url
     *
     * @param ArticleInterface $article
     * @return string
     */
    public function getArticleUrl(ArticleInterface $article)
    {
        return $this->url->getArticleUrl($article);
    }

    /**
     * Returns back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->redirect->getRefererUrl();
    }

    /**
     * Highlights query words in string
     *
     * @param string $highlightedString
     * @return array|string|string[]|null
     */
    public function highlightSearchQueryWords($highlightedString)
    {
        preg_match_all('~\w+~', (string)$this->getSearchQuery(), $match);

        $matchWords = [];
        foreach ($match[0] as $matchWord) {
            if (strlen($matchWord) >= 2) {
                $matchWords[] = $matchWord;
            }
        }

        if ($match) {
            $pattern = '~\\b(' . implode('|', $matchWords) . ')~i';
            $result = preg_replace($pattern, '<strong>$0</strong>', $highlightedString);
        } else {
            $result = $highlightedString;
        }

        return $result;
    }

    /**
     * Returns error message if any
     *
     * @return string|null
     */
    public function getErrorMessage()
    {
        return null;
    }
}
