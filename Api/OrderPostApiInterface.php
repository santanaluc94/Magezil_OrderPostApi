<?php

namespace Magezil\OrderPostApi\Api;

interface OrderPostApiInterface
{
    const CUSTOMER = "customer";

    const CUSTOMER_NAME = "name";
    const CUSTOMER_TAXVAT = "cpf_cnpj";
    const CUSTOMER_SOCIAL_REASON = "razao_social";
    const CUSTOMER_FANTASY_NAME = "nome_fantasia";
    const CUSTOMER_STATE_REGISTRATION = "insc_estadual";
    const CUSTOMER_DOB = "dob";
    const CUSTOMER_PHONE = "telephone";

    const SHIPPING_ADDRESS = "shipping_address";

    const SHIPPING_ADDRESS_STREET = "street";
    const SHIPPING_ADDRESS_NUMBER = "number";
    const SHIPPING_ADDRESS_ADDITIONAL = "additional";
    const SHIPPING_ADDRESS_NEIGHBORHOOD = "neighborhood";
    const SHIPPING_ADDRESS_CITY = "city";
    const SHIPPING_ADDRESS_CITY_CODE = "city_ibge_code";
    const SHIPPING_ADDRESS_UF = "uf";

    const ITEMS = "items";

    const ITEM_SKU = "sku";
    const ITEM_NAME = "name";
    const ITEM_PRICE = "price";
    const ITEM_QTY = "qty";

    const SHIPPING_METHOD = "shipping_method";
    const PAYMENT_METHOD = "payment_method";
    const INSTALLMENTS = "installments";
    const SUBTOTAL = "subtotal";
    const SHIPPING_AMOUNT = "shipping_amount";
    const TOTAL_DISCOUNTS = "total_discounts";
    const TOTALS = "total";
}
