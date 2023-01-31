<?php
namespace Aheadworks\FaqFree\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\FaqFree\Model\Article\Validator;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\DateResolver;

class Article extends AbstractDb
{
    public const MAIN_TABLE_NAME = 'aw_faq_article';
    public const FAQ_ARTICLE_TAG_TABLE_NAME = 'aw_faq_article_tag';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var DateResolver
     */
    private $dateResolver;

    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param EntityManager $entityManager
     * @param Validator $validator
     * @param DateResolver $dateResolver
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        Validator $validator,
        DateResolver $dateResolver,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->dateResolver = $dateResolver;
    }

    /**
     * Article constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, ArticleInterface::ARTICLE_ID);
    }

    /**
     * Return Id of Article by Url-key
     *
     * @param string $urlKey
     * @return int|null
     */
    public function getIdByUrlKey($urlKey)
    {
        $select = $this->getConnection()->select()
            ->from(['cp' => $this->getMainTable()])
            ->where('cp.url_key = ?', $urlKey)
            ->limit(1);
        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Unset category id from articles
     *
     * @param int $categoryId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function unsetCategoryIdFromArticles($categoryId)
    {
        $select = $this->getConnection()->select()
            ->from(['article' => $this->getMainTable()])
            ->where('article.category_id = ?', $categoryId);
        $articles = $this->getConnection()->fetchAll($select);
        if ($articles) {
            foreach ($articles as &$article) {
                $article[ArticleInterface::IS_ENABLE] = 0;
            }
            $this->getConnection()->insertOnDuplicate($this->getMainTable(), $articles);
        }
    }

    /**
     * Save an object
     *
     * @param AbstractModel $object
     * @return $this
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * Delete an object
     *
     * @param AbstractModel $object
     * @return $this
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param int $articleId
     * @param string|null $field
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(AbstractModel $object, $articleId, $field = null)
    {
        $this->entityManager->load($object, $articleId);

        return $this;
    }

    /**
     * Get validation rules
     *
     * @return mixed
     */
    public function getValidationRulesBeforeSave()
    {
        return $this->validator;
    }
}
