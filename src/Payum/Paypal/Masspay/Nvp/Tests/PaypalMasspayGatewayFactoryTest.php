<?php

namespace Payum\Paypal\Masspay\Nvp\Tests;

use Payum\Core\Exception\LogicException;
use Payum\Core\Gateway;
use Payum\Core\Tests\AbstractGatewayFactoryTest;
use Payum\Paypal\Masspay\Nvp\PaypalMasspayGatewayFactory;

class PaypalMasspayGatewayFactoryTest extends AbstractGatewayFactoryTest
{
    public function testShouldAddDefaultConfigPassedInConstructorWhileCreatingGatewayConfig()
    {
        $factory = new PaypalMasspayGatewayFactory([
            'foo' => 'fooVal',
            'bar' => 'barVal',
        ]);

        $config = $factory->createConfig();

        $this->assertIsArray($config);

        $this->assertArrayHasKey('foo', $config);
        $this->assertSame('fooVal', $config['foo']);

        $this->assertArrayHasKey('bar', $config);
        $this->assertSame('barVal', $config['bar']);
    }

    public function testShouldConfigContainDefaultOptions()
    {
        $factory = new PaypalMasspayGatewayFactory();

        $config = $factory->createConfig();

        $this->assertIsArray($config);

        $this->assertArrayHasKey('payum.default_options', $config);
        $this->assertEquals(
            [
                'username' => '',
                'password' => '',
                'signature' => '',
                'sandbox' => true,
            ],
            $config['payum.default_options']
        );
    }

    public function testShouldConfigContainFactoryNameAndTitle()
    {
        $factory = new PaypalMasspayGatewayFactory();

        $config = $factory->createConfig();

        $this->assertIsArray($config);

        $this->assertArrayHasKey('payum.factory_name', $config);
        $this->assertSame('paypal_masspay_nvp', $config['payum.factory_name']);

        $this->assertArrayHasKey('payum.factory_title', $config);
        $this->assertSame('PayPal Masspay', $config['payum.factory_title']);
    }

    public function testShouldThrowIfRequiredOptionsNotPassed()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The username, password, signature fields are required.');
        $factory = new PaypalMasspayGatewayFactory();

        $gateway = $factory->create();

        $this->assertInstanceOf(Gateway::class, $gateway);
    }

    protected function getGatewayFactoryClass(): string
    {
        return PaypalMasspayGatewayFactory::class;
    }

    protected function getRequiredOptions(): array
    {
        return [
            'username' => 'aName',
            'password' => 'aPass',
            'signature' => 'aSign',
        ];
    }
}
