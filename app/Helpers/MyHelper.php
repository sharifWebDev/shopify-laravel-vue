<?php

use Ramsey\Uuid\Type\Integer;

    function getShopifyURLForStore($endpoint, $store) {
        info('getShopifyURLForStore', [checkIfStoreIsPrivate($store) ?
            'https://'.$store['api_key'].':'.$store['api_secret_key'].'@'.$store['myshopify_domain'].'/admin/api/'.config('shopify.shopify_api_version').'/'.$endpoint
            : 
            'https://'.$store['myshopify_domain'].'/admin/api/'.config('shopify.shopify_api_version').'/'.$endpoint]);


        return checkIfStoreIsPrivate($store) ?
            'https://'.$store['api_key'].':'.$store['api_secret_key'].'@'.$store['myshopify_domain'].'/admin/api/'.config('shopify.shopify_api_version').'/'.$endpoint
            :
            'https://'.$store['myshopify_domain'].'/admin/api/'.config('shopify.shopify_api_version').'/'.$endpoint;
    }

    function getShopifyHeadersForStore($store, $method = 'GET') {
        info('touch  --------- VerifyShopify middleware handle method getShopifyHeadersForStore--',
            [$method == 'GET' ? [
                'Content-Type' => 'application/json',
                'X-Shopify-Access-Token' => $store['access_token']
            ] : [
                'Content-Type: application/json',
                'X-Shopify-Access-Token: '.$store['access_token']
            ]]);

        return $method == 'GET' ? [
            'Content-Type' => 'application/json',
            'X-Shopify-Access-Token' => $store['access_token']
        ] : [
            'Content-Type: application/json',
            'X-Shopify-Access-Token: '.$store['access_token']
        ];
    }

    function getGraphQLHeadersForStore($store) {
        return checkIfStoreIsPrivate($store) ? [
            'Content-Type' => 'application/json',
            'X-Shopify-Access-Token' => $store['api_secret_key'],
            'X-GraphQL-Cost-Include-Fields' => true
        ] : [
            'Content-Type' => 'application/json',
            'X-Shopify-Access-Token' => $store['access_token'],
            'X-GraphQL-Cost-Include-Fields' => true
        ];
    }

    function checkIfStoreIsPrivate($store) {
        return isset($store['api_key']) && isset($store['api_secret_key'])
                && $store['api_key'] !== null && $store['api_secret_key'] !== null
                && strlen($store['api_key']) > 0 && strlen($store['api_secret_key']) > 0;
    }

    function getCurrencySymbol($code) {
        switch($code) {
            case 'INR': return '₹ ';
            case 'EUR': return '€ ';
            case 'UAH': return '₴ ';
            case 'PLN': return 'zł ';
            case 'RON': return 'Lei ';
            case 'CZK': return 'Kč ';
            case 'SEK': return 'SEK ';
            case 'HUF': return 'Ft ';
            case 'BYN': return 'BYN ';
            case 'BGN': return 'лв. ';
            case 'DKK': return 'DKK ';
            case 'NOK': return 'NOK ';
            case 'HRK': return 'kn ';
            case 'MDL': return 'L ';
            case 'BAM': return 'KM ';
            case 'ALL': return 'Lek ';
            case 'MKD': return 'ден ';
            case 'ISK': return 'kr ';
            case 'SAR': return 'ر.س';
            case 'ARS':
            case 'CAD': return 'C$ ';
            case 'NZD': return 'NZ$ ';
            case 'CLP':
            case 'COP':
            case 'MXN':
            case 'SGD': return 'S$ ';
            case 'AUD': return 'A$ ';
            case 'USD': return '$ ';
            case 'GBP': return '£ ';
            case 'CHF': return 'CHF ';
            case 'ZAR': return 'S ';
            case 'RUB': return '₽ ';
            case 'QAR': return 'ر.ق ';
            case 'MUR':
            case 'NPR': return '₨ ';
            case 'MYR': return 'RM ';
            case 'KPW':
            case 'KRW': return '₩ ';
            case 'JPY': return '¥ ';
            case 'IDR': return 'Rp ';
            case 'VND': return '₫ ';
            case 'KWD': return 'د.ك ';
            case 'AED': return 'د.إ ';
            case 'OMR': return 'ر.ع. ';
            case 'BOB': return '$ ';
            case 'AZN': return '₼ ';
            case 'THB': return '฿ ';
            default : return $code;
        }
    }

    function determineIfAppIsEmbedded() {
        return config('shopify.app_embedded') == 'true' || config('shopify.app_embedded') == true;
    }

if (!function_exists('checkAdmin')) {
    function checkAdmin($record):bool
    {
        return $record->id === auth()->id();
    }
}

if (!function_exists('hasShopifyStore')) { 
    function hasShopifyStore($record):bool
    {
        return $record->myshopify_domain ? true : false;
    }
}

if (!function_exists('checkAdminOrHasShopifyStore')) { 
    function checkAdminOrHasShopifyStore($record):bool
    { 
        return $record->id === auth()->id() || $record->myshopify_domain;
    }
}
if (!function_exists('checkHasTotalDeletedRecords')) { 
    function checkHasTotalDeletedRecords($records):int
    {
        $recordsToDelete = $records->filter(function ($record) { 
            return !checkAdminOrHasShopifyStore($record);
        });
        return count($recordsToDelete);
    }
}
    