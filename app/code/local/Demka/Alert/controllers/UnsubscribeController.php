<?php

class Demka_Alert_UnsubscribeController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var int|null
     */
    private $productId = null;
    /**
     * @var Mage_Catalog_Model_Product|null
     */
    private $product = null;
    /**
     * @var string|null
     */
    private $customerEmail = null;
    /**
     * @var Mage_Catalog_Model_Session|null
     */
    private $session = null;

    private function setParameters()
    {
        $productId = $this->getRequest()->getParam('product');
        $customerEmail = $this->getRequest()->getParam('email');
        $this->productId = !empty($productId) ? (int)$productId : null;
        $this->customerEmail = !empty($customerEmail) ? $customerEmail : null;
        $this->session = Mage::getSingleton('catalog/session');
    }

    public function stockAllAction()
    {
        $this->setParameters();

        if (!$this->customerEmail) {
            $this->_redirect('/');
            return;
        }

        try {
            /** @var Demka_Alert_Model_Stock $model */
            $model = Mage::getModel('demkaalert/stock');
            /** @var Demka_Alert_Model_Resource_Stock_Collection $collection */
            $collection = $model->getCollection();
            $collection
                ->addFieldToFilter('customer_email', $this->customerEmail)
                ->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId());

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->session->addSuccess($this->__('You will no longer receive stock alerts.'));
        } catch (Exception $e) {
            $this->session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl('/');
    }

    public function priceAllAction()
    {
        $this->setParameters();

        if (!$this->customerEmail) {
            $this->_redirect('/');
            return;
        }

        try {
            /** @var Demka_Alert_Model_Price $model */
            $model = Mage::getModel('demkaalert/price');
            /** @var Demka_Alert_Model_Resource_Price_Collection $collection */
            $collection = $model->getCollection();
            $collection
                ->addFieldToFilter('customer_email', $this->customerEmail)
                ->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId());

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->session->addSuccess($this->__('You will no longer receive price alerts for this product.'));
        } catch (Exception $e) {
            $this->session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl('/');
    }

    public function stockAction()
    {
        $this->setParameters();

        if (!$this->productId || !$this->customerEmail) {
            $this->_redirect('/');
            return;
        }

        if (!$this->loadProduct()) {
            $this->session->addError($this->__('The product is not found.'));
            $this->_redirect('/');
            return;
        }

        try {
            /** @var Demka_Alert_Model_Stock $model */
            $model = Mage::getModel('demkaalert/stock');
            /** @var Demka_Alert_Model_Resource_Stock_Collection $collection */
            $collection = $model->getCollection();
            $collection
                ->addFieldToFilter('product_id', $this->productId)
                ->addFieldToFilter('customer_email', $this->customerEmail)
                ->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId());

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->session->addSuccess($this->__('You will no longer receive stock alerts for this product.'));
        } catch (Exception $e) {
            $this->session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($this->product->getProductUrl());
    }

    public function priceAction()
    {
        $this->setParameters();

        if (!$this->productId || !$this->customerEmail) {
            $this->_redirect('/');
            return;
        }

        if (!$this->loadProduct()) {
            $this->session->addError($this->__('The product is not found.'));
            $this->_redirect('/');
            return;
        }

        try {
            /** @var Demka_Alert_Model_Price $model */
            $model = Mage::getModel('demkaalert/price');
            /** @var Demka_Alert_Model_Resource_Price_Collection $collection */
            $collection = $model->getCollection();
            $collection
                ->addFieldToFilter('product_id', $this->productId)
                ->addFieldToFilter('customer_email', $this->customerEmail)
                ->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId());

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->session->addSuccess($this->__('The alert subscription has been deleted.'));
        } catch (Exception $e) {
            $this->session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($this->product->getProductUrl());

    }

    private function loadProduct(): bool
    {
        $this->product = Mage::getModel('catalog/product')->load($this->productId);
        return $this->product->getId() && $this->product->isVisibleInCatalog();
    }

}

