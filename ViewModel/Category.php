<?php
namespace Aheadworks\FaqFree\ViewModel;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\Url;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\FaqFree\Model\Page\Request\InstanceCreator;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\FaqFree\Model\CategoryTree as CategoryTreeModel;
use Aheadworks\FaqFree\Api\CategoryManagementInterface as FaqCategoryManagement;
use Magento\Widget\Model\Template\Filter as TemplateFilter;

class Category implements ArgumentInterface
{
    /**
     * @var InstanceCreator
     */
    private $pageInstanceCreator;

    /**
     * @var FaqCategoryManagement
     */
    private $faqCategoryManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var TemplateFilter
     */
    private $templateFilter;

    /**
     * Category constructor.
     * @param InstanceCreator $pageInstanceCreator
     * @param FaqCategoryManagement $faqCategoryManager
     * @param StoreManagerInterface $storeManager
     * @param Url $url
     * @param TemplateFilter $templateFilter
     */
    public function __construct(
        InstanceCreator $pageInstanceCreator,
        FaqCategoryManagement $faqCategoryManager,
        StoreManagerInterface $storeManager,
        Url $url,
        TemplateFilter $templateFilter
    ) {
        $this->pageInstanceCreator = $pageInstanceCreator;
        $this->faqCategoryManager = $faqCategoryManager;
        $this->storeManager = $storeManager;
        $this->url = $url;
        $this->templateFilter = $templateFilter;
    }

    /**
     * Return category title
     *
     * @return string|null
     */
    public function getTitle()
    {
        $category = $this->pageInstanceCreator->getCurrentCategory();

        return $category
            ? $category->getName()
            : null;
    }

    /**
     * Get faq url manager
     *
     * @return Url
     */
    public function getUrlManager()
    {
        return $this->url;
    }

    /**
     * Return category title
     *
     * @return string|null
     */
    public function getContent()
    {
        $category = $this->pageInstanceCreator->getCurrentCategory();

        return $category && $category->getContent()
            ? $this->templateFilter->filter($category->getContent())
            : null;
    }

    /**
     * Return category tree
     *
     * @return CategoryTreeModel
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryTree()
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var CategoryInterface $category */
        $category = $this->pageInstanceCreator->getCurrentCategory();

        return $this->faqCategoryManager->buildCategoryTree($storeId, $category);
    }
}
