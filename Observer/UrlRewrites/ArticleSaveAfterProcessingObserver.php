<?php
namespace Aheadworks\FaqFree\Observer\UrlRewrites;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\FaqFree\Model\UrlRewrites\Processor\Save\Entity\Article as RewritesProcessor;
use Aheadworks\FaqFree\Api\Data\ArticleInterfaceFactory as ArticleFactory;
use Magento\Framework\Exception\LocalizedException;

class ArticleSaveAfterProcessingObserver implements ObserverInterface
{
    /**
     * @var RewritesProcessor
     */
    private $rewritesProcessor;

    /**
     * @var ArticleFactory
     */
    private $articleFactory;

    /**
     * ArticleSaveAfterProcessingObserver constructor.
     * @param ArticleFactory $articleFactory
     * @param RewritesProcessor $rewritesProcessor
     */
    public function __construct(
        ArticleFactory $articleFactory,
        RewritesProcessor $rewritesProcessor
    ) {
        $this->articleFactory = $articleFactory;
        $this->rewritesProcessor = $rewritesProcessor;
    }

    /**
     * Process rewrites after article saved
     *
     * @param EventObserver $observer
     * @return $this
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        /** @var ArticleInterface $article */
        $article = $observer->getEvent()->getEntity();

        if ($article && !empty($article->getOrigData())) {
           /** @var ArticleInterface $origArticle */
            $origArticle = $this->articleFactory->create(['data' => $article->getOrigData()]);
        } else {
            $origArticle = null;
        }

        if ($article) {
            $this->rewritesProcessor->process($article, $origArticle);
        }

        return $this;
    }
}
