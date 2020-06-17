<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Signature;

/**
 * Generates secret key.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class SecretKeyGenerator
{
    public const PROD_URL = 'https://e-commerce.raiffeisen.ru/portal/mrchtrnvw/trn_xml.jsp';
    public const TEST_URL = 'https://test.ecom.raiffeisen.ru/portal/mrchtrnvw/trn_xml.jsp';

    public function __construct($url = self::PROD_URI)
    {
        $this->url = $url;
    }

    public static function prod(): self
    {
        return new self(self::PROD_URL);
    }

    public static function test(): self
    {
        return new self(self::TEST_URL);
    }

    public function generate(string $merchandId, string $login, string $password): SecretKey
    {
        $curl = \curl_init($this->url);
        \curl_setopt_array($curl, [
            \CURLOPT_POST => true,
            \CURLOPT_RETURNTRANSFER => true,
            \CURLOPT_POSTFIELDS => [
                'xICBSXPProxy.ReqType' => 100,
                'xICBSXPProxy.Version' => 05.00,
                'xICBSXPProxy.UserName' => $login,
                'xICBSXPProxy.UserPassword' => $password,
                'MerchantID' => $merchandId,
            ],
        ]);

        $secretKey = \curl_exec($curl);

        return SecretKey::base64($secretKey);
    }
}
