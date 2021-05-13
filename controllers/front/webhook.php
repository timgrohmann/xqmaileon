<?php

use PrestaShop\Module\XQMaileon\Transactions\AbandonedCart;
use PrestaShop\Module\XQMaileon\Transactions\AbandonedCartTransactionService;
use Symfony\Component\HttpFoundation\Response;

class XQMaileonWebhookModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $requestBodyJson = json_decode(file_get_contents('php://input'), true);

        $webhookResult = $this->handleWebhook($requestBodyJson);
        if ($webhookResult !== false) {
            $this->context->smarty->assign("myarray", $webhookResult);
        } else {
            http_response_code(400);
            exit;
        }


        # raw json output. probably not needed for webhook but it fixes errors and is good for debugging
        header("Content-Type: application/json");
        $this->setTemplate('module:xqmaileon/views/templates/front/webhook.tpl');
    }

    public function postProcess()
    {

    }

    /**
     * @return bool Returns true if webhook was handled successfully.
     */
    public function handleWebhook($body) {
        $type = $_GET['type'];
        
        switch ($type) {
            case 'doi':
                return $this->handleDoiConfirm($body);
            case 'unsubscribe':
                return $this->handleUnsubscribe($body);
            case 'cartTest':
                return $this->handleCartTest();
            default:
                return false;
        }
    }

    public function handleDoiConfirm($body) {
        $id = intval($body['external_id']);
        $customer = new \Customer($id);
        $customer->doi = true;
        return $customer->update();
    }

    public function handleUnsubscribe($body)
    {
        $id = intval($body['external_id']);
        $customer = new \Customer($id);
        $customer->doi = false;
        $customer->newsletter = false;
        return $customer->update();
    }

    public function handleCartTest()
    {
        $carts = (new AbandonedCartTransactionService())->notify();


        $this->context->smarty->assign("myarray", $carts);

        return $carts;
    }
}
