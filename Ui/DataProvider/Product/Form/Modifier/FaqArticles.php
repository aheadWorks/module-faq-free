<?php
namespace Aheadworks\FaqFree\Ui\DataProvider\Product\Form\Modifier;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Aheadworks\FaqFree\Model\Article\Listing\Builder as ArticleListBuilder;

class FaqArticles extends AbstractModifier
{
    private const FAQ_ARTICLES_ATTRIBUTE_GROUP_CODE = 'faq-articles';

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ArticleListBuilder
     */
    private $articleListBuilder;

    /**
     * FaqArticles constructor.
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param ArticleListBuilder $articleListBuilder
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ArticleListBuilder $articleListBuilder
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->articleListBuilder = $articleListBuilder;
    }

    /**
     * Changing data on the product editing form
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        /** @var Product $product */
        $product = $this->locator->getProduct();

        try {
            $this->articleListBuilder
                ->getSearchCriteriaBuilder()
                ->addFilter(ArticleInterface::PRODUCT_IDS, $product->getId());
            $articles = $this->articleListBuilder->getArticleList();
        } catch (LocalizedException $e) {
            $articles = [];
        }

        $data[$product->getId()][self::DATA_SOURCE_DEFAULT][ProductAttributeInterface::CODE_AW_FAQ_ARTICLES] =
            $this->convertArticlesToArray($articles);

        return $data;
    }

    /**
     * Convert article to article data array
     *
     * @param ArticleInterface[] $articles
     * @return array
     */
    private function convertArticlesToArray($articles)
    {
        $result = [];

        foreach ($articles as $article) {
            $result[] = [
                'id' => $article->getArticleId(),
                'category_id' => $article->getCategoryId(),
                'title' => $article->getTitle(),
                'url_key' => $article->getUrlKey()
            ];
        }

        return $result;
    }

    /**
     * Modifying the display of attributes on the product editing form
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        unset($meta[self::FAQ_ARTICLES_ATTRIBUTE_GROUP_CODE]['children']);

        $meta = array_replace_recursive(
            $meta,
            [
                self::FAQ_ARTICLES_ATTRIBUTE_GROUP_CODE => [
                    'children' => [
                        'faq_articles_header_container' => $this->getFaqArticlesHeaderContainer(),
                        'faq_articles_modal' => $this->getFaqArticlesModal(),
                        'faq_articles_attached_fieldset' => $this->getFaqArticlesAttachedFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'sortOrder' => 1000,
                            ],
                        ],
                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * Returns faq articles modal
     *
     * @return array
     */
    private function getFaqArticlesModal()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'isTemplate' => false,
                        'options' => [
                            'title' => __('Pick FAQ articles'),
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        [
                                            'targetName' => '${ $.name }',
                                            'actionName' => 'closeModal',
                                            '__disableTmpl' => ['targetName' => false],
                                        ]
                                    ]
                                ],
                                [
                                    'text' => __('Add selected articles'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = faq_articles_listing',
                                            'actionName' => 'save',
                                        ],
                                        [
                                            'targetName' => '${ $.name }',
                                            'actionName' => 'closeModal',
                                            '__disableTmpl' => ['targetName' => false],
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
            ],
            'children' => [
                'faq_articles_listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'insertListing',
                                'autoRender' => false,
                                'dataScope' => 'faq_articles_listing',
                                'ns' => 'faq_article_listing',
                                'externalProvider' => 'faq_article_listing.faq_article_listing_data_source',
                                'selectionsProvider'
                                    => 'faq_article_listing.faq_article_listing.faq_article_columns.ids',
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true,
                                ],
                                'externalFilterMode' => true,
                                'sortOrder' => 20
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Returns faq articles header container
     *
     * @return array
     */
    private function getFaqArticlesHeaderContainer()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => '',
                        'content' => __('You can attach FAQ articles to product'),
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => 10
                    ],
                ],
            ],
            'children' => [
                'add_products_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'title' => __('Pick FAQ articles'),
                                'component' => 'Magento_Ui/js/form/components/button',
                                'sortOrder' => 10,
                                'actions' => [
                                    [
                                        'targetName' => 'product_form.product_form.' .
                                            self::FAQ_ARTICLES_ATTRIBUTE_GROUP_CODE . '.faq_articles_modal',
                                        'actionName' => 'openModal',
                                    ],
                                    [
                                        'targetName' => 'product_form.product_form.' .
                                            self::FAQ_ARTICLES_ATTRIBUTE_GROUP_CODE .
                                            '.faq_articles_modal.faq_articles_listing',
                                        'actionName' => 'render'
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Returns faq articles attached fieldset
     *
     * @return array
     */
    private function getFaqArticlesAttachedFieldset()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'opened' => true,
                        'label' => '',
                    ],
                ],
            ],
            'children' => [
                ProductAttributeInterface::CODE_AW_FAQ_ARTICLES => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => DynamicRows::NAME,
                                'label' => '',
                                'sortOrder' => 10,
                                'additionalClasses' => 'admin__field-wide',
                                'dndConfig' => ['enabled' => false],
                                'component' => 'Aheadworks_FaqFree/js/ui/form/components/dynamic-rows-grid-paginated',
                                'template' => 'ui/dynamic-rows/templates/grid',
                                'addButton' => false,
                                'deleteButtonLabel' => __('Remove'),
                                'itemTemplate' => 'record',
                                'columnsHeader' => false,
                                'columnsHeaderAfterRender' => true,
                                'dataProvider' => self::DATA_SCOPE_PRODUCT . '.' . 'faq_articles_listing',
                                'map' => [
                                    'id' => 'article_id',
                                    'category_id' => 'category_id',
                                    'title' => 'title',
                                    'url_key' => 'url_key',
                                ],
                                'links' => [
                                    'insertData' => '${ $.provider }:${ $.dataProvider }',
                                    '__disableTmpl' => ['insertData' => false],
                                ]
                            ]
                        ]
                    ],
                    'children' => [
                        'record' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => 'container',
                                        'componentType' => 'container',
                                        'isTemplate' => true,
                                        'is_collection' => true,
                                        'component' => 'Magento_Ui/js/dynamic-rows/record',
                                    ]
                                ]
                            ],
                            'children' => [
                                'id' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'formElement' => Input::NAME,
                                                'componentType' => Field::NAME,
                                                'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                                'dataType' => Text::NAME,
                                                'dataScope' => 'id',
                                                'fit' => true,
                                                'sortOrder' => 10,
                                                'label' => __('ID')
                                            ]
                                        ]
                                    ]
                                ],
                                'title' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'formElement' => Input::NAME,
                                                'componentType' => Field::NAME,
                                                'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                                'dataType' => Text::NAME,
                                                'dataScope' => 'title',
                                                'fit' => true,
                                                'sortOrder' => 20,
                                                'label' => __('Article Name')
                                            ]
                                        ]
                                    ]
                                ],
                                'url_key' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'formElement' => Input::NAME,
                                                'componentType' => Field::NAME,
                                                'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                                'dataType' => Text::NAME,
                                                'dataScope' => 'url_key',
                                                'fit' => true,
                                                'sortOrder' => 30,
                                                'label' => __('Url Key')
                                            ]
                                        ]
                                    ]
                                ],
                                'category_id' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'formElement' => Input::NAME,
                                                'componentType' => Field::NAME,
                                                'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                                'dataType' => Text::NAME,
                                                'dataScope' => 'category_id',
                                                'fit' => true,
                                                'sortOrder' => 40,
                                                'label' => __('Category ID')
                                            ]
                                        ]
                                    ]
                                ],
                                'actionDelete' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'additionalClasses' => 'data-grid-actions-cell',
                                                'componentType' => 'actionDelete',
                                                'dataType' => Text::NAME,
                                                'label' => __('Actions'),
                                                'sortOrder' => 70,
                                                'fit' => true,
                                            ],
                                        ],
                                    ],
                                ],
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
