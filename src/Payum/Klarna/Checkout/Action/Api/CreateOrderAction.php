<?php

namespace Payum\Klarna\Checkout\Action\Api;

use Klarna_Checkout_Order;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Klarna\Checkout\Request\Api\CreateOrder;

class CreateOrderAction extends BaseApiAwareAction
{
    /**
     * @param CreateOrder $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $this->addMerchantId($model);

        $this->callWithRetry(function () use ($model, $request) {
            $order = new Klarna_Checkout_Order($this->getConnector());
            $order->create($model->toUnsafeArray());

            $request->setOrder($order);
        });
    }

    public function supports($request)
    {
        return $request instanceof CreateOrder;
    }
}
