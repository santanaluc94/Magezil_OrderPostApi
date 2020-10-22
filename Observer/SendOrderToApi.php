<?php

namespace Magezil\OrderPostApi\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

use Magezil\OrderPostApi\Model\Config\Settings as OrderPostSettings;
use Magezil\OrderPostApi\Logger\Logger;

class SendOrderToApi implements ObserverInterface
{
    private $orderPostSettings;
    private $logger;

    public function __construct(
        OrderPostSettings $orderPostSettings,
        Logger $logger
    ) {
        $this->orderPostSettings = $orderPostSettings;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        if ($this->orderPostSettings->isEnabled()) {
            try {

                $this->logger->info(__('Order sent to API.'));
            } catch (\Exception $exception) {

                $this->logger->error(__('It was not possible to send the order to API.'));
            }
        } else {
            $this->logger->warning(__('Module is disable! The order can not send to API.'));
        }
    }
}
