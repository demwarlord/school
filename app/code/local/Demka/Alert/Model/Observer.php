<?php

/**
 * Rewrite of ProductAlert observer
 */
class Demka_Alert_Model_Observer extends Mage_ProductAlert_Model_Observer
{
    /**
     * @param Mage_ProductAlert_Model_Email $email
     * @return $this
     */
    protected function _processPrice(Mage_ProductAlert_Model_Email $email): self
    {
        // call ProductAlert module process
        parent::_processPrice($email);

        // doing ours process
        try {
            $this->processCommon(Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_PRICE);
        } catch (Exception $e) {
            $this->_errors[] = $e->getMessage();
        }
        return $this;
    }

    /**
     * @param Mage_ProductAlert_Model_Email $email
     * @return $this
     */
    protected function _processStock(Mage_ProductAlert_Model_Email $email): self
    {
        // call ProductAlert module process
        parent::_processStock($email);

        // doing ours process
        try {
            $this->processCommon(Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_STOCK);
        } catch (Exception $e) {
            $this->_errors[] = $e->getMessage();
        }
        return $this;
    }

    /**
     * @param string $type
     * @throws Mage_Core_Model_Store_Exception
     */
    private function processCommon(string $type)
    {
        /** @var Demka_Alert_Model_Email $demkaEmail */
        $demkaEmail = Mage::getModel('demkaalert/email');

        $demkaEmail->setType($type);
        $originalStore = Mage::app()->getStore();

        /** @var Mage_Core_Model_Website $website */
        foreach ($this->_getWebsites() as $website) {

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }

            if (!Mage::getStoreConfig(
                self::XML_PATH_PRICE_ALLOW,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )) {
                continue;
            }

            $collection = $this->getAlertCollection($type, $website->getId());

            $previousCustomerEmail = null;
            $demkaEmail->setWebsite($website);
            Mage::app()->setCurrentStore($website->getDefaultGroup()->getDefaultStore());

            foreach ($collection as $alert) {
                try {
                    if (empty($previousCustomerEmail) || $previousCustomerEmail != $alert->getCustomerEmail()) {
                        $customerEmail = $alert->getCustomerEmail();

                        if (!empty($previousCustomerEmail)) {
                            $demkaEmail->send();
                        }

                        $previousCustomerEmail = $customerEmail;
                        $demkaEmail->clean();
                        $demkaEmail->setCustomerEmail($customerEmail);
                    } else {
                        $customerEmail = $previousCustomerEmail;
                    }

                    /* @var Mage_Catalog_Model_Product $product */
                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($website->getDefaultStore()->getId())
                        ->load($alert->getProductId());

                    if (!$product) {
                        continue;
                    }

                    if ($type === Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_PRICE
                        && $alert->getPrice() > $product->getFinalPrice()) {
                        $productPrice = $product->getFinalPrice();
                        $product->setFinalPrice(Mage::helper('tax')->getPrice($product, $productPrice));
                        $product->setPrice(Mage::helper('tax')->getPrice($product, $product->getPrice()));
                        $demkaEmail->addPriceProduct($product);
                        $alert->setPrice($productPrice);
                        $alert->setLastSendDate(Mage::getModel('core/date')->gmtDate());
                        $alert->setSendCount($alert->getSendCount() + 1);
                        $alert->setStatus(1);
                        $alert->save();
                    } elseif ($type === Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_STOCK &&
                        $product->isSalable()) {
                        $demkaEmail->addStockProduct($product);
                        $alert->setSendDate(Mage::getModel('core/date')->gmtDate());
                        $alert->setSendCount($alert->getSendCount() + 1);
                        $alert->setStatus(1);
                        $alert->save();
                    }

                } catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }

            if (!empty($previousCustomerEmail)) {
                try {
                    $demkaEmail->send();
                } catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }

            Mage::app()->setCurrentStore($originalStore);
        }
    }

    /**
     * @param string $type
     * @param int $websiteId
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    private function getAlertCollection(string $type, int $websiteId): Mage_Core_Model_Mysql4_Collection_Abstract
    {
        if ($type === Demka_Alert_Model_Email::EMAIL_ALERT_TYPE_STOCK) {
            /** @var Demka_Alert_Model_Resource_Price_Collection $collection */
            $collection = Mage::getModel('demkaalert/stock')->getCollection();
            $collection->addFieldToFilter('status', 0);
        } else {
            /** @var Demka_Alert_Model_Resource_Price_Collection $collection */
            $collection = Mage::getModel('demkaalert/price')->getCollection();
        }
        $collection->addFieldToFilter('website_id', $websiteId);
        $collection->setOrder('customer_email');

        return $collection;
    }
}
