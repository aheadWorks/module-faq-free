<?php
namespace Aheadworks\FaqFree\Model\ResourceModel;

use Magento\Framework\DB\Helper\Mysql\Fulltext;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\FaqFree\Model\Category\Validator;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterfaceFactory as CategoryFactory;
use Magento\Framework\Model\DataObject;

/**
 * Faq category mysql resource
 */
class Category extends AbstractDb
{
    public const MAIN_TABLE_NAME = 'aw_faq_category';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var Fulltext
     */
    private $fulltext;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param EntityManager $entityManager
     * @param Validator $validator
     * @param CategoryFactory $categoryFactory
     * @param Fulltext $fulltext
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        Validator $validator,
        CategoryFactory $categoryFactory,
        Fulltext $fulltext,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->categoryFactory = $categoryFactory;
        $this->fulltext = $fulltext;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, CategoryInterface::CATEGORY_ID);
    }

    /**
     * Return Id of Category by Url-key
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
     * Save object data
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     * @throws AlreadyExistsException
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * Delete the object
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
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
     * @param int $categoryId
     * @param string|null $field
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(AbstractModel $object, $categoryId, $field = null)
    {
        $this->entityManager->load($object, $categoryId);
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

    /**
     * Perform actions before object save
     *
     * @param AbstractModel|DataObject $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $object->unsetData(CategoryInterface::PATH);
        }
        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel|DataObject $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        if (empty($object->getPath()) && !$object->getParentId()) {
            $this->getConnection()->update(
                $this->getTable(self::MAIN_TABLE_NAME),
                [CategoryInterface::PATH => $object->getId()],
                [CategoryInterface::CATEGORY_ID . ' = ?' => $object->getId()]
            );
        } elseif (empty($object->getPath()) && $object->getParentId()) {
            $this->updatePathByParent($object);
        }
        return parent::_afterSave($object);
    }

    /**
     * Update path by parent category
     *
     * @param CategoryInterface $category
     */
    private function updatePathByParent($category)
    {
        $select = $this->getConnection()->select()
            ->from([$this->getTable(self::MAIN_TABLE_NAME)], [CategoryInterface::PATH])
            ->where(CategoryInterface::CATEGORY_ID . ' = ?', $category->getParentId());

        $parentCategoryPath = $select->getConnection()->fetchOne($select);

        $this->getConnection()->update(
            $this->getTable(self::MAIN_TABLE_NAME),
            [CategoryInterface::PATH => $parentCategoryPath . '/' . $category->getCategoryId()],
            [CategoryInterface::CATEGORY_ID . ' = ?' => $category->getCategoryId()]
        );
    }

    /**
     * Return all child categories
     *
     * @param int $categoryId
     * @return array
     */
    public function getAllChildCategories($categoryId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable(self::MAIN_TABLE_NAME))
            ->where(CategoryInterface::PATH . ' LIKE ?', $categoryId . '/%')
            ->orWhere(CategoryInterface::PATH . ' LIKE ?', '%/' . $categoryId . '/%');

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * Search Categories with query string
     *
     * @param string $searchString
     * @param int|null $limit
     * @return array
     */
    public function searchQuery($searchString, $limit = null)
    {
        $matchQuery = $this->fulltext->getMatchQuery(
            [CategoryInterface::NAME],
            $searchString,
            Fulltext::FULLTEXT_MODE_BOOLEAN
        );

        $query = $this
            ->getConnection()
            ->select()
            ->from($this->getTable(self::MAIN_TABLE_NAME), CategoryInterface::CATEGORY_ID)
            ->where($matchQuery)
            ->limit($limit);

        return $this->getConnection()->fetchAll($query);
    }
}
