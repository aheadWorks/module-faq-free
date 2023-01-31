<?php
namespace Aheadworks\FaqFree\ViewModel\CategoryTree;

use Aheadworks\FaqFree\Model\CategoryTree as CategoryTreeModel;
use Aheadworks\FaqFree\Api\CategoryManagementInterface as FaqCategoryManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

abstract class AbstractCategoryTree implements ArgumentInterface
{
    /**
     * @var FaqCategoryManagement
     */
    private $faqCategoryManager;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TreeFormatter
     */
    private $treeFormatter;

    /**
     * AbstractCategoryTree constructor.
     * @param FaqCategoryManagement $faqCategoryManager
     * @param JsonSerializer $jsonSerializer
     * @param StoreManagerInterface $storeManager
     * @param TreeFormatter $treeFormatter
     */
    public function __construct(
        FaqCategoryManagement $faqCategoryManager,
        JsonSerializer $jsonSerializer,
        StoreManagerInterface $storeManager,
        TreeFormatter $treeFormatter
    ) {
        $this->faqCategoryManager = $faqCategoryManager;
        $this->jsonSerializer = $jsonSerializer;
        $this->storeManager = $storeManager;
        $this->treeFormatter = $treeFormatter;
    }

    /**
     * Return category tree items json
     *
     * @return bool|string
     */
    public function getCategoryTreeJson()
    {
        try {
            /** @var CategoryTreeModel $categoryTree */
            $categoryTree = $this->faqCategoryManager->buildCategoryTree($this->storeManager->getStore()->getId());
            $categoryTreeFormattedArray = $this->prepareTreeFormatter($this->treeFormatter)->format($categoryTree);
        } catch (LocalizedException $e) {
            $categoryTreeFormattedArray = [];
        }

        return $this->jsonSerializer->serialize($categoryTreeFormattedArray);
    }

    /**
     * Return page title
     *
     * @return string|null
     */
    abstract public function getPageTitle();

    /**
     * Prepare tree formatter if necessary
     *
     * @param TreeFormatter $treeFormatter
     * @return TreeFormatter
     */
    abstract protected function prepareTreeFormatter($treeFormatter);
}
