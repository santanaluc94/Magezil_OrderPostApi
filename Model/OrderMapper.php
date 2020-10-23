<?php

namespace Magezil\OrderPostApi\Model;

use Magezil\OrderPostApi\Logger\Logger;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magezil\OrderPostApi\Api\OrderPostApiInterface;
use Magento\Sales\Model\Order;
use Magento\Customer\Model\Data\Customer;

class OrderMapper
{
    protected $logger;
    protected $customerRepository;

    public function __construct(
        Logger $logger,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
    }

    public function execute(Order $order): ?array
    {
        // The attributes CUSTOMER_SOCIAL_REASON, CUSTOMER_FANTASY_NAME, CUSTOMER_STATE_REGISTRATION must to be created in customer to saved
        // The attribute INSTALLMENTS must to be created in order to be saved

        try {
            $customer = $this->getCustomerById((int) $order->getCustomerId());
            $address = $order->getShippingAddress();

            $data = [
                OrderPostApiInterface::CUSTOMER => [
                    OrderPostApiInterface::CUSTOMER_NAME => $customer->getFirstname() . " " . $customer->getLastname(),
                    OrderPostApiInterface::CUSTOMER_TAXVAT => $this->setMask(
                        strlen($customer->getTaxvat()) == 14 ? '##.###.###/####-##' : '###.###.###-##',
                        $customer->getTaxvat()
                    ),
                    OrderPostApiInterface::CUSTOMER_DOB => date("d/m/Y", strtotime($customer->getDob())),
                    OrderPostApiInterface::CUSTOMER_PHONE => $this->setMask(
                        strlen($address->getTelephone()) == 10 ? '(##) ####-####' : '(##) ####-#####',
                        $address->getTelephone()
                    ),
                ],
                OrderPostApiInterface::SHIPPING_ADDRESS => [
                    OrderPostApiInterface::SHIPPING_ADDRESS_STREET => explode(',', $address->getStreet()[0])[0],
                    OrderPostApiInterface::SHIPPING_ADDRESS_NUMBER => explode(',', $address->getStreet()[0])[1],
                    OrderPostApiInterface::SHIPPING_ADDRESS_NEIGHBORHOOD => $address->getStreet()[1],
                    OrderPostApiInterface::SHIPPING_ADDRESS_ADDITIONAL => $address->getStreet()[2],
                    OrderPostApiInterface::SHIPPING_ADDRESS_CITY => $address->getCity(),
                    OrderPostApiInterface::SHIPPING_ADDRESS_CITY_CODE => $address->getRegionId(),
                    OrderPostApiInterface::SHIPPING_ADDRESS_UF => $address->getRegion(),
                ],
                OrderPostApiInterface::SHIPPING_METHOD => $order->getShippingMethod(),
                OrderPostApiInterface::PAYMENT_METHOD => $order->getPayment()->getMethod(),
                OrderPostApiInterface::SUBTOTAL => $order->getSubtotal(),
                OrderPostApiInterface::SHIPPING_AMOUNT => $order->getShippingAmount(),
                OrderPostApiInterface::TOTAL_DISCOUNTS => $order->getDiscountAmount(),
                OrderPostApiInterface::TOTALS => $order->getGrandTotal(),
            ];

            foreach ($order->getAllItems() as $item) {
                $data[OrderPostApiInterface::ITEMS][] = [
                    OrderPostApiInterface::ITEM_SKU => $item->getSku(),
                    OrderPostApiInterface::ITEM_NAME => $item->getName(),
                    OrderPostApiInterface::ITEM_PRICE => $item->getPrice(),
                    OrderPostApiInterface::ITEM_QTY => $item->getQtyOrdered()
                ];
            }
        } catch (\Exception $exception) {
            $this->logger->error(__('An error occurred while trying to map the order fields.'));
            return null;
        }

        return $data;
    }

    private function getCustomerById(int $customerId): ?Customer
    {
        return $this->customerRepository->getById($customerId);
    }

    public function setMask(string $mask, string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = str_replace(" ", "", $value);

        for ($i = 0; $i < strlen($value); $i++) {
            $mask[strpos($mask, "#")] = $value[$i];
        }

        return $mask;
    }
}
