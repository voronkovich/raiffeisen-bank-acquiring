<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Signature;

use Voronkovich\RaiffeisenBankAcquiring\MerchantIdFormatter;

/**
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class SignatureGenerator
{
    private $key;

    public function __construct(SecretKey $key)
    {
        $this->key = $key;
    }

    public static function base64(string $key): self
    {
        return new self(SecretKey::base64($key));
    }

    public static function hex(string $key): self
    {
        return new self(SecretKey::hex($key));
    }

    public function generatePaymentSignature(array $data): Signature
    {
        $chunks = MerchantIdFormatter::parse($data['MerchantID']);
        $chunks[] = $data['PurchaseDesc'];

        if (isset($data['PCurrencyCode'])) {
            $chunks[] = $data['PCurrencyCode'];
        }

        $chunks[] = $data['PPurchaseAmt'] ?? $data['PurchaseAmt'];

        if (isset($data['Time']) && isset($data['Window'])) {
            $chunks[] = $data['Time'];
            $chunks[] = $data['Window'];
        }

        return $this->generate($chunks);
    }

    public function generateCallbackSignature(array $data): Signature
    {
        return $this->generate([ $data['descr'], $data['amt'], $data['result'] ]);
    }

    private function generate(array $chunks): Signature
    {
        $signature = \hash_hmac('sha256', \implode(';', $chunks), $this->key->getValue(), true);

        return new Signature($signature);
    }
}
