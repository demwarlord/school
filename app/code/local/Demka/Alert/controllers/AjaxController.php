<?php

class Demka_Alert_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function getFormPriceAction()
    {
        $this->_showForm(['block' => 'price']);
    }

    private function _showForm($data = false)
    {
        /** @var Mage_Core_Block_Template $form */
        $form = $this->getLayout()->createBlock('core/template', 'custom.form',
            ['template' => 'demkaalert/ajax.phtml']);

        if ($data) {
            $form->assign($data);
        }

        $this->getResponse()->setBody($form->toHtml());
    }

    public function getFormStockAction()
    {
        $this->_showForm(['block' => 'stock']);
    }

    public function priceAction()
    {
        $this->_process('price');
    }

    private function _process($table)
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
                    case 'price':
                        $model = Mage::getModel('demkaalert/price')
                            ->setCustomerEmail($email)
                            ->setProductId($product->getId())
                            ->setPrice($product->getFinalPrice())
                            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                            ->setAddDate(Mage::getModel('core/date')->gmtDate())
                            ->setStatus(0);
                        $model->save();
                        break;
                    case 'stock':
                        $model = Mage::getModel('demkaalert/stock')
                            ->setCustomerEmail($email)
                            ->setProductId($product->getId())
                            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                            ->setAddDate(Mage::getModel('core/date')->gmtDate())
                            ->setStatus(0);
                        $model->save();
                        break;
                }

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

    public function stockAction()
    {
        $this->_process('stock');
    }
}