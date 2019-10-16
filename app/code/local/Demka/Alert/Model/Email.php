<?php

class Demka_Alert_Model_Email extends Mage_Core_Model_Abstract
{
    const EMAIL_ALERT_TYPE_PRICE = 'price';
    const EMAIL_ALERT_TYPE_STOCK = 'stock';
    const XML_PATH_EMAIL_IDENTITY = 'catalog/productalert/email_identity';
    const XML_PATH_EMAIL_PRICE_TEMPLATE_ANONYMOUS = 'catalog/productalert/email_price_template_anonymous';
    const XML_PATH_EMAIL_STOCK_TEMPLATE_ANONYMOUS = 'catalog/productalert/email_stock_template_anonymous';
    /**
     * @var string
     */
    protected $type = null;
    /**
     * @var Mage_Core_Model_Website|null
     */
    protected $website = null;
    /**
     * @var string|null
     */
    protected $customerEmail = null;
    /**
     * @var string
     */
    protected $customerName = 'Anonymous';
    /**
     * @var array
     */
    protected $priceProducts = array();
    /**
     * @var array
     */
    protected $stockProducts = array();

    /**
     * @var Demka_Alert_Block_Email_Price
     */
    protected $priceBlock;

    /**
     * @var Demka_Alert_Block_Email_Stock
     */
    protected $stockBlock;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param Mage_Core_Model_Website $website
     * @return $this
     */
    public function setWebsite(Mage_Core_Model_Website $website): self
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @param int $websiteId
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setWebsiteId(int $websiteId): self
    {
        $this->website = Mage::app()->getWebsite($websiteId);
        return $this;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setCustomerEmail(string $email): self
    {
        $this->customerEmail = $email;
        return $this;
    }

    /**
     * @return $this
     */
    public function clean(): self
    {
        $this->customerEmail = null;
        $this->priceProducts = array();
        $this->stockProducts = array();

        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function addPriceProduct(Mage_Catalog_Model_Product $product): self
    {
        $this->priceProducts[$product->getId()] = $product;
        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function addStockProduct(Mage_Catalog_Model_Product $product): self
    {
        $this->stockProducts[$product->getId()] = $product;
        return $this;
    }

    /**
     * @return bool
     */
    public function send(): bool
    {
        if (is_null($this->website) || is_null($this->customerEmail)) {
            return false;
        }

        if (($this->type == self::EMAIL_ALERT_TYPE_PRICE && count($this->priceProducts) == 0)
            || ($this->type == self::EMAIL_ALERT_TYPE_STOCK && count($this->stockProducts) == 0)
        ) {
            return false;
        }
        if (!$this->website->getDefaultGroup() || !$this->website->getDefaultGroup()->getDefaultStore()) {
            return false;
        }

        $store = $this->website->getDefaultGroup()->getDefaultStore();
        $storeId = $store->getId();

        if ($this->type == self::EMAIL_ALERT_TYPE_PRICE &&
            !Mage::getStoreConfig(self::XML_PATH_EMAIL_PRICE_TEMPLATE_ANONYMOUS, $storeId)) {
            return false;
        } elseif ($this->type == self::EMAIL_ALERT_TYPE_STOCK &&
            !Mage::getStoreConfig(self::XML_PATH_EMAIL_STOCK_TEMPLATE_ANONYMOUS, $storeId)) {
            return false;
        }

        if ($this->type != self::EMAIL_ALERT_TYPE_PRICE && $this->type != self::EMAIL_ALERT_TYPE_STOCK) {
            return false;
        }

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
        Mage::app()->getTranslator()->init('frontend', true);

        if ($this->type == self::EMAIL_ALERT_TYPE_PRICE) {
            $block = $this->_getPriceBlock()
                ->assign([
                    'products' => $this->priceProducts,
                    'customer_email' => $this->customerEmail
                ])
                ->toHtml();
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_PRICE_TEMPLATE_ANONYMOUS, $storeId);
        } else {
            $block = $this->_getStockBlock()
                ->assign([
                    'products' => $this->stockProducts,
                    'customer_email' => $this->customerEmail
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
                $this->customerEmail,
                $this->customerName,
                array(
                    'customerName' => $this->customerName,
                    'alertGrid' => $block
                )
            );

        return true;
    }

    private function _getPriceBlock(): Demka_Alert_Block_Email_Price
    {
        if (is_null($this->priceBlock)) {
            $class = Mage::getConfig()->getBlockClassName('demkaalert/email_price');
            $this->priceBlock = new $class;
            $this->priceBlock->setTemplate('email/demkaalert/price.phtml');
        }
        return $this->priceBlock;
    }

    private function _getStockBlock(): Demka_Alert_Block_Email_Stock
    {
        if (is_null($this->stockBlock)) {
            $class = Mage::getConfig()->getBlockClassName('demkaalert/email_stock');
            $this->stockBlock = new $class;
            $this->stockBlock->setTemplate('email/demkaalert/stock.phtml');
        }
        return $this->stockBlock;
    }
}
