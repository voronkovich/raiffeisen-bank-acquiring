<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Payment;

class PaymentForm
{
    const PROD_URL = 'https://e-commerce.raiffeisen.ru/vsmc3ds/pay_check/3dsproxy_init.jsp';
    const TEST_URL = 'https://test.ecom.raiffeisen.ru/vsmc3ds/pay_check/3dsproxy_init.jsp';

    private $data;
    private $label;
    private $attributes;

    public function __construct(array $data, string $label = 'Pay', array $attributes = [])
    {
        $this->data = $data;
        $this->label = $label;
        $this->attributes = \array_replace([
            'action' => self::PROD_URL,
            'method' => 'POST',
        ], $attributes);
    }

    public function render(): string
    {
        return \sprintf('<form%s>%s</form>', $this->renderAttributes(), $this->renderBody());
    }

    public function __toString(): string
    {
        return $this->render();
    }

    private function renderAttributes(): string
    {
        $attributes = '';
        foreach ($this->attributes as $name => $value) {
            $attributes .= \sprintf(' %s="%s"', $name, $value);
        }

        return $attributes;
    }

    private function renderBody(): string
    {
        return $this->renderFields().$this->renderButton();
    }

    private function renderFields(): string
    {
        $fields = '';
        foreach ($this->data as $name => $value) {
            $fields .= $this->renderField($name, $value);
        }

        return $fields;
    }

    private function renderField($name, $value): string
    {
        return \sprintf('<input type="hidden" name="%s" value="%s" />', $name, $value);
    }

    private function renderButton(): string
    {
        return \sprintf('<input type="submit" value="%s" />', $this->label);
    }
}
