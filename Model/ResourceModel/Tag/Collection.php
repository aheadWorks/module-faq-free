<?php
namespace Aheadworks\FaqFree\Model\ResourceModel\Tag;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\TagInterface;
use Aheadworks\FaqFree\Model\Tag;
use Aheadworks\FaqFree\Model\ResourceModel\Tag as ResourceTag;
use Aheadworks\FaqFree\Model\ResourceModel\Article as ResourceArticle;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Tag collection constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Tag::class, ResourceTag::class);
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray(TagInterface::ID, TagInterface::NAME);
    }

    /**
     * Attach article ids
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        if (!$this->isLoaded()) {
            $this->attachArticleIds();
        }
        return parent::_beforeLoad();
    }

    /**
     * Attach article ids
     *
     * @return $this
     */
    private function attachArticleIds()
    {
        $this->getSelect()->joinLeft(
            ['tag_article_table' => $this->getTable(ResourceArticle::FAQ_ARTICLE_TAG_TABLE_NAME)],
            'main_table.id = tag_article_table.tag_id',
            [ArticleInterface::ARTICLE_ID => 'tag_article_table.article_id']
        )->group(TagInterface::ID);

        return $this;
    }
}
