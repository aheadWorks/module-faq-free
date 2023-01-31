<?php
namespace Aheadworks\FaqFree\ViewModel\Article;

use Aheadworks\FaqFree\Model\Page\Request\InstanceCreator;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\FaqFree\Model\Article\StructuredData\ProviderInterface;
use Magento\Store\Model\StoreManagerInterface;

class StructuredData implements ArgumentInterface
{
    /**
     * @var InstanceCreator
     */
    private $pageInstanceCreator;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProviderInterface
     */
    private $dataProvider;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * StructuredData constructor.
     * @param InstanceCreator $pageInstanceCreator
     * @param ProviderInterface $dataProvider
     * @param JsonSerializer $jsonSerializer
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        InstanceCreator $pageInstanceCreator,
        ProviderInterface $dataProvider,
        JsonSerializer $jsonSerializer,
        StoreManagerInterface $storeManager
    ) {
        $this->pageInstanceCreator = $pageInstanceCreator;
        $this->dataProvider = $dataProvider;
        $this->jsonSerializer = $jsonSerializer;
        $this->storeManager = $storeManager;
    }

    /**
     * Return structured data for current article
     *
     * @return string
     */
    public function getStructuredDataForCurrentArticle()
    {
        $result = '';

        $currentArticle = $this->pageInstanceCreator->getCurrentArticle();
        $currentStore = (int) $this->storeManager->getStore()->getId();

        if ($currentArticle) {
            $data = $this->dataProvider->getData($currentArticle, $currentStore);
            $result = $this->jsonSerializer->serialize($data);
        }

        return $result;
    }
}
