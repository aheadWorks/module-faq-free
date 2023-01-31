<?php
namespace Aheadworks\FaqFree\Observer\UrlRewrites;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\FaqFree\Model\UrlRewrites\Processor\Save\Entity\Category as RewritesProcessor;
use Aheadworks\FaqFree\Api\Data\CategoryInterfaceFactory as CategoryFactory;
use Magento\Framework\Exception\LocalizedException;

class CategorySaveAfterProcessingObserver implements ObserverInterface
{
    /**
     * @var RewritesProcessor
     */
    private $rewritesProcessor;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * CategorySaveAfterProcessingObserver constructor.
     * @param CategoryFactory $categoryFactory
     * @param RewritesProcessor $rewritesProcessor
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        RewritesProcessor $rewritesProcessor
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->rewritesProcessor = $rewritesProcessor;
    }

    /**
     * Process rewrites after category saved
     *
     * @param EventObserver $observer
     * @return $this
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        /** @var CategoryInterface $category */
        $category = $observer->getEvent()->getEntity();

        if ($category && !empty($category->getOrigData())) {
            /** @var CategoryInterface $origCategory */
            $origCategory = $this->categoryFactory->create(['data' => $category->getOrigData()]);
        } else {
            $origCategory = null;
        }

        if ($category) {
            $this->rewritesProcessor->process($category, $origCategory);
        }

        return $this;
    }
}
