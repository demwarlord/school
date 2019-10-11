<?php

class Demka_Alert_Model_Email extends Mage_Core_Model_Abstract
{
    const XML_PATH_EMAIL_PRICE_TEMPLATE_ANONYMOUS = 'catalog/productalert/email_price_template_anonymous';
    const XML_PATH_EMAIL_STOCK_TEMPLATE_ANONYMOUS = 'catalog/productalert/email_stock_template_anonymous';
    const XML_PATH_EMAIL_IDENTITY = 'catalog/productalert/email_identity';

    protected $_type = 'price';

    /** @var Mage_Core_Model_Website $_website */
    protected $_website;
    protected $_customerEmail;
    protected $_customerName = 'Anonymous';
    protected $_priceProducts = array();
    protected $_stockProducts = array();

    /** @var Demka_Alert_Block_Email_Price $_priceBlock */
    protected $_priceBlock;

    /** @var Demka_Alert_Block_Email_Stock $_stockBlock */
    protected $_stockBlock;

    public function getType()
    {
        return $this->_type;
    }

    public function setType($type)
    {
        $this->_type = $type;
    }

    public function setWebsite(Mage_Core_Model_Website $website)
    {
        $this->_website = $website;
        return $this;
    }

    public function setWebsiteId($websiteId)
    {
        $this->_website = Mage::app()->getWebsite($websiteId);
        return $this;
    }

    public function setCustomerEmail($email)
    {
        $this->_customerEmail = $email;
        return $this;
    }

    public function clean()
    {
        $this->_customerEmail = null;
        $this->_priceProducts = array();
        $this->_stockProducts = array();

        return $this;
    }

    public function addPriceProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_priceProducts[$product->getId()] = $product;
        return $this;
    }

    public function addStockProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_stockProducts[$product->getId()] = $product;
        return $this;
    }

    public function send()
    {
        if (is_null($this->_website) || is_null($this->_customerEmail)) {
            return false;
        }

        if (($this->_type == 'price' && count($this->_priceProducts) == 0)
            || ($this->_type == 'stock' && count($this->_stockProducts) == 0)
        ) {
            return false;
        }
        if (!$this->_website->getDefaultGroup() || !$this->_website->getDefaultGroup()->getDefaultStore()) {
            return false;
        }

        $store = $this->_website->getDefaultGroup()->getDefaultStore();
        $storeId = $store->getId();

        if ($this->_type == 'price' &&
            !Mage::getStoreConfig(self::XML_PATH_EMAIL_PRICE_TEMPLATE_ANONYMOUS, $storeId)) {
            return false;
        } elseif ($this->_type == 'stock' &&
            !Mage::getStoreConfig(self::XML_PATH_EMAIL_STOCK_TEMPLATE_ANONYMOUS, $storeId)) {
            return false;
        }

        if ($this->_type != 'price' && $this->_type != 'stock') {
            return false;
        }

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
        Mage::app()->getTranslator()->init('frontend', true);

        if ($this->_type == 'price') {
            $block = $this->_getPriceBlock()
                ->assign([
                'products' => $this->_priceProducts,
                'customer_email' => $this->_customerEmail
                ])
                ->toHtml();
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_PRICE_TEMPLATE_ANONYMOUS, $storeId);
        } else {
            $block = $this->_getStockBlock()
                ->assign([
                'products' => $this->_stockProducts,
                'customer_email' => $this->_customerEmail
                ])
                ->toHtml();
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_STOCK_TEMPLATE_ANONYMOUS, $storeId);
        }

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $storeId
            ))->sendTransactional(
                $templateId,
                Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId),
                $this->_customerEmail,
                $this->_customerName,
                array(
                    'customerName' => $this->_customerName,
                    'alertGrid' => $block
                )
            );

        return true;
    }

    private function _getPriceBlock()
    {
        if (is_null($this->_priceBlock)) {
            $class = Mage::getConfig()->getBlockClassName('demkaalert/email_price');
            $this->_priceBlock = new $class;
            $this->_priceBlock->setTemplate('email/demkaalert/price.phtml');
        }
        return $this->_priceBlock;
    }

    private function _getStockBlock()
    {
        if (is_null($this->_stockBlock)) {
            $class = Mage::getConfig()->getBlockClassName('demkaalert/email_stock');
            $this->_stockBlock = new $class;
            $this->_stockBlock->setTemplate('email/demkaalert/stock.phtml');
        }
        return $this->_stockBlock;
    }
}
