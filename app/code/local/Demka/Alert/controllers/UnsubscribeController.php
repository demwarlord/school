<?php

class Demka_Alert_UnsubscribeController extends Mage_Core_Controller_Front_Action
{
    private $_productId = null;

    /* @var Mage_Catalog_Model_Product $_product */
    private $_product = null;
    private $_customerEmail = null;
    /* @var Mage_Catalog_Model_Session $_session */
    private $_session = null;

    public function priceAction()
    {
        $this->_setParams();
        if (!$this->_productId || !$this->_customerEmail) {
            $this->_redirect('');
            return;
        }

        if (!$this->_loadProduct()) {
            return;
        }

        try {
            /** @var Demka_Alert_Model_Price $model */
            $model = Mage::getModel('demkaalert/price');
            /** @var Demka_Alert_Model_Resource_Price_Collection $collection */
            $collection = $model->getCollection();
            $collection
                ->addFieldToFilter('product_id', $this->_productId)
                ->addFieldToFilter('customer_email', $this->_customerEmail)
                ->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId());

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->_session->addSuccess($this->__('The alert subscription has been deleted.'));
        } catch (Exception $e) {
            $this->_session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($this->_product->getProductUrl());

    }

    private function _setParams()
    {
        $productId = $this->getRequest()->getParam('product');
        $customerEmail = $this->getRequest()->getParam('email');
        $this->_productId = !empty($productId) ? (int)$productId : null;
        $this->_customerEmail = !empty($customerEmail) ? $customerEmail : null;
        $this->_session = Mage::getSingleton('catalog/session');
    }

    private function _loadProduct()
    {
        $this->_product = Mage::getModel('catalog/product')->load($this->_productId);
        if (!$this->_product->getId() || !$this->_product->isVisibleInCatalog()) {
            Mage::getSingleton('customer/session')->addError($this->__('The product is not found.'));
            $this->_redirect('customer/account/');
            return false;
        }
        return true;
    }

    public function priceAllAction()
    {
        $this->_setParams();

        if (!$this->_customerEmail) {
            $this->_redirect('');
            return;
        }

        try {
            /** @var Demka_Alert_Model_Price $model */
            $model = Mage::getModel('demkaalert/price');
            /** @var Demka_Alert_Model_Resource_Price_Collection $collection */
            $collection = $model->getCollection();
            $collection
                ->addFieldToFilter('customer_email', $this->_customerEmail)
                ->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId());

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->_session->addSuccess($this->__('You will no longer receive price alerts for this product.'));
        } catch (Exception $e) {
            $this->_session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl('/');
    }

    public function stockAction()
    {
        $this->_setParams();
        if (!$this->_productId || !$this->_customerEmail) {
            $this->_redirect('');
            return;
        }

        if (!$this->_loadProduct()) {
            return;
        }

        try {
            /** @var Demka_Alert_Model_Stock $model */
            $model = Mage::getModel('demkaalert/stock');
            /** @var Demka_Alert_Model_Resource_Stock_Collection $collection */
            $collection = $model->getCollection();
            $collection
                ->addFieldToFilter('product_id', $this->_productId)
                ->addFieldToFilter('customer_email', $this->_customerEmail)
                ->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId());

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->_session->addSuccess($this->__('You will no longer receive stock alerts for this product.'));
        } catch (Exception $e) {
            $this->_session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($this->_product->getProductUrl());
    }

    public function stockAllAction()
    {
        $this->_setParams();

        if (!$this->_customerEmail) {
            $this->_redirect('');
            return;
        }

        try {
            /** @var Demka_Alert_Model_Stock $model */
            $model = Mage::getModel('demkaalert/stock');
            /** @var Demka_Alert_Model_Resource_Stock_Collection $collection */
            $collection = $model->getCollection();
            $collection
                ->addFieldToFilter('customer_email', $this->_customerEmail)
                ->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId());

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->_session->addSuccess($this->__('You will no longer receive stock alerts.'));
        } catch (Exception $e) {
            $this->_session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl('/');
    }
}
