<?php

use PrestaShop\Module\XQMaileon\Transactions\AbandonedCart;
use PrestaShop\Module\XQMaileon\Transactions\AbandonedCartTransactionService;
use Symfony\Component\HttpFoundation\Response;

class XQMaileonWebhookModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        header("Content-Type: application/json");

        # request body sent by maileon is json, decode into array
        $requestBodyJson = json_decode(file_get_contents('php://input'), true);

        $webhookResult = $this->handleWebhook($requestBodyJson);
        if ($webhookResult === false) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Webhook failed. Did you specify a type?'
            ]);
            exit;
        }


        # return webhook result which can be utilized for logging/debugging
        echo json_encode($webhookResult);
        exit;
    }

    /**
     * @return bool Returns true if webhook was handled successfully.
     */
    public function handleWebhook($body)
    {
        $type = $_GET['type'];

        switch ($type) {
            case 'doi':
                return $this->handleDoiConfirm($body);
            case 'unsubscribe':
                return $this->handleUnsubscribe($body);
            default:
                return false;
        }
    }

    public function handleDoiConfirm($body)
    {
        $id = intval($body['external_id']);
        $customer = new \Customer($id);
        if (empty($customer->email)) return 'No customer for given id.';
        $customer->optin = true;
        return $customer->update();
    }

    public function handleUnsubscribe($body)
    {
        $id = intval($body['external_id']);
        $customer = new \Customer($id);
        if (empty($customer->email)) return 'No customer for given id.';
        $customer->optin = false;
        $customer->newsletter = false;
        return $customer->update();
    }
}
