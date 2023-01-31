<?php
declare(strict_types=1);

namespace Aheadworks\FaqFree\Plugin\CustomerData;

use Aheadworks\FaqFree\Model\Config;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\CustomerData\Customer;

class CustomerPlugin
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param Config $config
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        Config $config
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->config = $config;
    }

    /**
     * Add required Faq configuration fields
     *
     * @param Customer $subject
     * @param string[] $result
     * @return string[]
     */
    public function afterGetSectionData(Customer $subject, array $result): array
    {
        $result['isFaqEnabled'] = !$this->config->isDisabledFaqForCurrentCustomer();
        $result['isNavigationMenuLinkEnabled'] = $this->config->isNavigationMenuLinkEnabled();

        if ($this->currentCustomer->getCustomerId()) {
            $customer = $this->currentCustomer->getCustomer();
            $result['email'] = $customer->getEmail();
        }

        return $result;
    }
}
