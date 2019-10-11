<?php

class Demka_Alert_Block_Product_View extends Mage_ProductAlert_Block_Product_View
{
    private $_currentUrl = null;
    private $_currentProductId = null;

    public function _construct()
    {
        parent::_construct();

        $this->_currentUrl = Mage::helper('core/url')->getCurrentUrl();

        $product = Mage::registry('current_product');
        if ($product) {
            $this->_currentProductId = $product->getId();
        }
    }

    public function prepareStockAlertData()
    {
        parent::prepareStockAlertData();
        $this->setDemkaBlock('stock');
    }

    public function preparePriceAlertData()
    {
        parent::preparePriceAlertData();
        $this->setDemkaBlock('price');
    }

    public function getCurrentProductId()
    {
        return $this->_currentProductId;
    }

    public function getCurrentUrl()
    {
        return $this->_currentUrl;
    }

    public function isLogged()
    {
        $customerId = Mage::getSingleton('customer/session')->getId();
        return !empty($customerId);
    }
}
