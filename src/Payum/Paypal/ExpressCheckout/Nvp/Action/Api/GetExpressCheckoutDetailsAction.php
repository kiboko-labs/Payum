<?php

namespace Payum\Paypal\ExpressCheckout\Nvp\Action\Api;

use ArrayAccess;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\GetExpressCheckoutDetails;

class GetExpressCheckoutDetailsAction implements ActionInterface, ApiAwareInterface
{
    use ApiAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    public function execute($request)
    {
        /** @var GetExpressCheckoutDetails $request */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        if (false == $model['TOKEN']) {
            throw new LogicException('TOKEN must be set. Have you run SetExpressCheckoutAction?');
        }

        $model->replace(
            $this->api->getExpressCheckoutDetails([
                'TOKEN' => $model['TOKEN'],
            ])
        );
    }

    public function supports($request)
    {
        return $request instanceof GetExpressCheckoutDetails &&
            $request->getModel() instanceof ArrayAccess
        ;
    }
}
