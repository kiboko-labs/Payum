<?php

namespace Payum\Stripe\Action\Api;

use ArrayAccess;
use Composer\InstalledVersions;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareTrait;
use Payum\Stripe\Constants;
use Payum\Stripe\Keys;
use Payum\Stripe\Request\Api\CreateCharge;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class CreateChargeAction implements ActionInterface, ApiAwareInterface
{
    use ApiAwareTrait {
        setApi as _setApi;
    }
    use GatewayAwareTrait;

    /**
     * @deprecated BC will be removed in 2.x. Use $this->api
     *
     * @var Keys
     */
    protected $keys;

    public function __construct()
    {
        $this->apiClass = Keys::class;
    }

    public function setApi($api)
    {
        $this->_setApi($api);

        // BC. will be removed in 2.x
        $this->keys = $this->api;
    }

    public function execute($request)
    {
        /** @var CreateCharge $request */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (false == ($model['card'] || $model['customer'])) {
            throw new LogicException('The either card token or customer id has to be set.');
        }

        if (is_array($model['card'])) {
            throw new LogicException('The token has already been used.');
        }

        try {
            Stripe::setApiKey($this->keys->getSecretKey());

            if (class_exists(InstalledVersions::class)) {
                Stripe::setAppInfo(
                    Constants::PAYUM_STRIPE_APP_NAME,
                    InstalledVersions::getVersion('stripe/stripe-php'),
                    Constants::PAYUM_URL
                );
            }

            $charge = Charge::create($model->toUnsafeArrayWithoutLocal());

            $model->replace($charge->toArray(true));
        } catch (ApiErrorException $e) {
            $model->replace($e->getJsonBody());
        }
    }

    public function supports($request)
    {
        return $request instanceof CreateCharge &&
            $request->getModel() instanceof ArrayAccess
        ;
    }
}
