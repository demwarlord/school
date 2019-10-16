<?php

class Demka_Alert_Block_Product_View extends Mage_ProductAlert_Block_Product_View
{
    /**
     * @var string|null
     */
    private $_currentUrl = null;
    /**
     * @var int|null
     */
    private $_currentProductId = null;

    public function _construct()
    {
        parent::_construct();

        $this->_currentUrl = Mage::helper('core/url')->getCurrentUrl();

        $product = Mage::registry('current_product');
        if ($product) {
            $this->_currentProductId = (int)$product->getId();
        }
    }

    public function prepareStockAlertData()
    {
        parent::prepareStockAlertData();
        $this->setDemkaBlock(Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_STOCK);
    }

    public function preparePriceAlertData()
    {
        parent::preparePriceAlertData();
        $this->setDemkaBlock(Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_PRICE);
    }

    /**
     * @return int
     */
    public function getCurrentProductId(): int
    {
        return $this->_currentProductId;
    }

    /**
     * @return string
     */
    public function getCurrentUrl(): string
    {
        return $this->_currentUrl;
    }

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        $customerId = Mage::getSingleton('customer/session')->getId();
        return !empty($customerId);
    }
}
