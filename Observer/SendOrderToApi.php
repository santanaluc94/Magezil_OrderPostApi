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
    const AUTHORIZATION_BEARER = 'Authorization: Bearer ';
    const TYPE_APPLICATION = 'POST';
    const CONTENT_TYPE_APPLICATION = 'Content-Type: application/json';

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

                $response = $this->sendOrderMapped($orderMapped, $endpoint, $apiKey);

                if ((int) $response['code'] === 400) {
                    throw new IntegrationException(__($response['message']));
                }

                $this->logger->info(__('Order %1 sent to API.', $order->getId()));
            } catch (IntegrationException $exception) {
                $this->logger->error($exception->getMessage());
            } catch (\Exception $exception) {
                $this->logger->error(__('It was not possible to send the order to API.'));
            }
        } else {
            $this->logger->warning(__('Module is disable! The order can not send to API.'));
        }
    }

    private function sendOrderMapped(array $body, string $endpoint, string $apiKey): array
    {
        $authorization = self::AUTHORIZATION_BEARER . $apiKey;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => self::TYPE_APPLICATION,
            CURLOPT_HTTPHEADER => [
                self::CONTENT_TYPE_APPLICATION,
                $authorization
            ],
            CURLOPT_POSTFIELDS, $body
        ]);

        $response = json_decode(curl_exec($curl));

        curl_close($curl);

        $this->logger->info(print_r($response, true));

        return $response;
    }
}
