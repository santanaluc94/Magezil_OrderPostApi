<?php

namespace Magezil\OrderPostApi\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magezil\OrderPostApi\Model\Config\Settings as OrderPostSettings;
use Magezil\OrderPostApi\Logger\Logger;
use Magezil\OrderPostApi\Model\OrderMapper;
use Magento\Framework\Exception\IntegrationException;

class SendOrderToApi implements ObserverInterface
{
    private $orderPostSettings;
    private $logger;
    private $orderMapper;

    public function __construct(
        OrderPostSettings $orderPostSettings,
        Logger $logger,
        OrderMapper $orderMapper
    ) {
        $this->orderPostSettings = $orderPostSettings;
        $this->logger = $logger;
        $this->orderMapper = $orderMapper;
    }

    public function execute(Observer $observer): void
    {
        if ($this->orderPostSettings->isEnabled()) {

            try {
                $order = $observer->getOrder();

                $orderMapped = $this->orderMapper->execute($order);

                if (is_null($orderMapped)) {
                    throw new \Exception();
                }

                $apiKey = $this->orderPostSettings->getApiKey();
                $endpoint = $this->orderPostSettings->getEndpoint();

                if (empty($apiKey) || empty($endpoint)) {
                    throw new IntegrationException(__('It is necessary to add values in system settings.'));
                }

                // Send order mapped to api here

                $this->logger->info(__('Order %1 sent to API.', $order->getId()));
            } catch (\Exception $exception) {
                $this->logger->error(__('It was not possible to send the order to API.'));
            }
        } else {
            $this->logger->warning(__('Module is disable! The order can not send to API.'));
        }
    }
}
