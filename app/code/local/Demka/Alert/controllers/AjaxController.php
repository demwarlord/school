<?php

class Demka_Alert_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function getFormPriceAction()
    {
        $this->showForm(['block' => Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_PRICE]);
    }

    public function getFormStockAction()
    {
        $this->showForm(['block' => Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_STOCK]);
    }

    public function priceAction()
    {
        $this->process(Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_PRICE);
    }

    public function stockAction()
    {
        $this->process(Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_STOCK);
    }

    private function showForm(array $data = [])
    {
        /** @var Mage_Core_Block_Template $form */
        $form = $this->getLayout()->createBlock('core/template', 'custom.form',
            ['template' => 'demkaalert/ajax.phtml']);

        if ($data) {
            $form->assign($data);
        }

        $this->getResponse()->setBody($form->toHtml());
    }

    private function process(string $table)
    {
        $session = Mage::getSingleton('catalog/session');

        $post = $this->getRequest()->getPost();

        if (!empty($post) && !empty($post['url']) && !empty($post['pid']) && !empty($post['email'])) {
            $email = $post['email'];
            $url = $post['url'];
            $pid = $post['pid'];

            $product = Mage::getModel('catalog/product')->load($pid);

            if (!$product->getId()) {
                /* @var $product Mage_Catalog_Model_Product */
                $session->addError($this->__('Not enough parameters.'));
                $this->_redirectUrl($url);
            }

            try {
                switch ($table) {
                    case Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_PRICE :
                        $model = Mage::getModel('demkaalert/price')
                            ->setPrice($product->getFinalPrice());
                        break;
                    default:
                        $model = Mage::getModel('demkaalert/stock');
                        break;
                }

                $model->setCustomerEmail($email)
                    ->setProductId($product->getId())
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->setAddDate(Mage::getModel('core/date')->gmtDate())
                    ->setStatus(0);
                $model->save();

                $session->addSuccess($this->__('The alert subscription has been saved.'));
                $this->_redirectUrl($url);
                return;
            } catch (Exception $e) {
                $session->addError($this->__('You have already signed up'));
                $this->_redirectUrl($url);
                return;
            }
        } else {
            $session->addError($this->__('Unable to submit your request. Try again later.'));
            $this->_redirect('/');
            return;
        }
    }
}