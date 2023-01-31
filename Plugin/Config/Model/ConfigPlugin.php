<?php
namespace Aheadworks\FaqFree\Plugin\Config\Model;

use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadataGenerator;
use Magento\Config\Model\Config;
use Aheadworks\FaqFree\Model\UrlRewrites\Service\Config as ConfigRewritesService;

class ConfigPlugin
{
    private const CONFIG_SECTION_NAME = 'faq';

    /**
     * @var UrlConfigMetadata[]
     */
    private $oldUrlConfigMetadata;

    /**
     * @var UrlConfigMetadataGenerator
     */
    private $urlConfigMetadataGenerator;

    /**
     * @var ConfigRewritesService
     */
    private $configRewritesService;

    /**
     * ConfigPlugin constructor.
     * @param UrlConfigMetadataGenerator $urlConfigMetadataGenerator
     * @param ConfigRewritesService $configRewritesService
     */
    public function __construct(
        UrlConfigMetadataGenerator $urlConfigMetadataGenerator,
        ConfigRewritesService $configRewritesService
    ) {
        $this->urlConfigMetadataGenerator = $urlConfigMetadataGenerator;
        $this->configRewritesService = $configRewritesService;
    }

    /**
     * Saves old url config metadata for generate url rewrites using it in afterSave
     *
     * @param Config $subject
     * @return null
     */
    public function beforeSave(
        Config $subject
    ) {
        if ($subject->getSection() == self::CONFIG_SECTION_NAME) {
            $this->oldUrlConfigMetadata = $this->urlConfigMetadataGenerator->generate(
                $subject->getWebsite(),
                $subject->getStore()
            );
        }

        return null;
    }

    /**
     * Init rewrite update processing
     *
     * @param Config $subject
     * @return null
     */
    public function afterSave(
        Config $subject
    ) {
        if ($subject->getSection() == self::CONFIG_SECTION_NAME && is_array($this->oldUrlConfigMetadata)) {
            $this->configRewritesService->updatePermanentRedirects($this->oldUrlConfigMetadata);
        }

        return null;
    }
}
