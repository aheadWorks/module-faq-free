<?php
namespace Aheadworks\FaqFree\ViewModel\CategoryTree;

use Aheadworks\FaqFree\Api\CategoryManagementInterface as FaqCategoryManagement;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\FaqFree\Model\Page\Request\InstanceCreator;

class CategoryPageCategoryTree extends AbstractCategoryTree
{
    /**
     * @var InstanceCreator
     */
    private $pageInstanceCreator;

    /**
     * CategoryPageCategoryTree constructor.
     * @param InstanceCreator $pageInstanceCreator
     * @param FaqCategoryManagement $faqCategoryManager
     * @param JsonSerializer $jsonSerializer
     * @param StoreManagerInterface $storeManager
     * @param TreeFormatter $treeFormatter
     */
    public function __construct(
        InstanceCreator $pageInstanceCreator,
        FaqCategoryManagement $faqCategoryManager,
        JsonSerializer $jsonSerializer,
        StoreManagerInterface $storeManager,
        TreeFormatter $treeFormatter
    ) {
        $this->pageInstanceCreator = $pageInstanceCreator;

        parent::__construct(
            $faqCategoryManager,
            $jsonSerializer,
            $storeManager,
            $treeFormatter
        );
    }

    /**
     * Return page title
     *
     * @return string|null
     */
    public function getPageTitle()
    {
        $currentCategory = $this->pageInstanceCreator->getCurrentCategory();

        return $currentCategory
            ? $currentCategory->getName()
            : null;
    }

    /**
     * Prepare tree formatter if necessary
     *
     * @param TreeFormatter $treeFormatter
     * @return TreeFormatter
     */
    protected function prepareTreeFormatter($treeFormatter)
    {
        return $treeFormatter->setCurrentCategory($this->pageInstanceCreator->getCurrentCategory());
    }
}
