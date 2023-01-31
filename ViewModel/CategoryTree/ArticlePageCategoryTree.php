<?php
namespace Aheadworks\FaqFree\ViewModel\CategoryTree;

use Aheadworks\FaqFree\Model\Page\Request\InstanceCreator;
use Aheadworks\FaqFree\Api\CategoryManagementInterface as FaqCategoryManagement;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class ArticlePageCategoryTree extends AbstractCategoryTree
{
    /**
     * @var InstanceCreator
     */
    private $pageInstanceCreator;

    /**
     * ArticlePageCategoryTree constructor.
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
        $currentArticle = $this->pageInstanceCreator->getCurrentArticle();

        return $currentArticle
            ? $currentArticle->getTitle()
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
        return $treeFormatter->setCurrentArticle($this->pageInstanceCreator->getCurrentArticle());
    }
}
