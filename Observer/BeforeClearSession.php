<?php
namespace Aheadworks\FaqFree\Observer;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Registry;
use Magento\Customer\Model\Visitor;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class BeforeClearSession implements ObserverInterface
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Visitor
     */
    private $visitor;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param Registry $registry
     * @param Visitor $visitor
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        Registry $registry,
        Visitor $visitor
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->registry = $registry;
        $this->visitor = $visitor;
    }

    /**
     * Store visitor id
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->currentCustomer->getCustomerId()) {
             $this->registry->register('aw_faq_visitor_id', $this->visitor->getId(), true);
        } else {
            $this->registry->register('aw_faq_customer_id', $this->currentCustomer->getCustomerId(), true);
        }

        return $this;
    }
}
