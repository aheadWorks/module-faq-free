<?php
namespace Aheadworks\FaqFree\Model\Source\Product\Attribute;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as DataObjectConverter;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface as ArticleRepository;

class Articles extends AbstractSource
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var DataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * Articles constructor.
     * @param DataObjectConverter $dataObjectConverter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        DataObjectConverter $dataObjectConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ArticleRepository $articleRepository
    ) {
        $this->dataObjectConverter = $dataObjectConverter;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Return options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $optionsArray = $this->getOptionsArray();
            $this->_options = $this->getOptionsFromArray($optionsArray);
        }
        return $this->_options;
    }

    /**
     * Retrieve options as array of objects
     *
     * @return ArticleInterface[]
     */
    private function getOptionsArray()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $articles = $this->articleRepository->getList($searchCriteria)->getItems();

        return $articles;
    }

    /**
     * Retrieve options array from the array of articles
     *
     * @param ArticleInterface[] $articlesArray
     * @return array
     */
    private function getOptionsFromArray($articlesArray)
    {
        return $this->dataObjectConverter->toOptionArray(
            $articlesArray,
            ArticleInterface::ARTICLE_ID,
            ArticleInterface::TITLE
        );
    }
}
