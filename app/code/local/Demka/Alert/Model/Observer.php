<?php
/**
 * Rewrite of ProductAlert observer
 */
class Demka_Alert_Model_Observer extends Mage_ProductAlert_Model_Observer
{

    protected function _processPrice(Mage_ProductAlert_Model_Email $email)
    {
        // call ProductAlert module process
        parent::_processPrice($email);

        // doing ours process
        /** @var Demka_Alert_Model_Email $demkaEmail */
        $demkaEmail = Mage::getModel('demkaalert/email');

        $demkaEmail->setType('price');
        $originalStore = Mage::app()->getStore();

        foreach ($this->_getWebsites() as $website) {
            /* @var $website Mage_Core_Model_Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }

            if (!Mage::getStoreConfig(
                self::XML_PATH_PRICE_ALLOW,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )) {
                continue;
            }

            try {
                /** @var Demka_Alert_Model_Resource_Price_Collection $collection */
                $collection = Mage::getModel('demkaalert/price')->getCollection();
                $collection->addFieldToFilter('website_id', $website->getId());
                $collection->setOrder('customer_email');
            } catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                return $this;
            }

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

                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($website->getDefaultStore()->getId())
                        ->load($alert->getProductId());

                    /* @var $product Mage_Catalog_Model_Product */
                    if (!$product) {
                        continue;
                    }

                    if ($alert->getPrice() > $product->getFinalPrice()) {
                        $productPrice = $product->getFinalPrice();
                        $product->setFinalPrice(Mage::helper('tax')->getPrice($product, $productPrice));
                        $product->setPrice(Mage::helper('tax')->getPrice($product, $product->getPrice()));
                        $demkaEmail->addPriceProduct($product);

                        $alert->setPrice($productPrice);
                        $alert->setLastSendDate(Mage::getModel('core/date')->gmtDate());
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
        }
        Mage::app()->setCurrentStore($originalStore);
        return $this;
    }

    protected function _processStock(Mage_ProductAlert_Model_Email $email)
    {
        // call ProductAlert module process
        parent::_processStock($email);

        // doing ours process
        /** @var Demka_Alert_Model_Email $demkaEmail */
        $demkaEmail = Mage::getModel('demkaalert/email');

        $demkaEmail->setType('stock');
        $originalStore = Mage::app()->getStore();

        foreach ($this->_getWebsites() as $website) {
            /* @var $website Mage_Core_Model_Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }

            if (!Mage::getStoreConfig(
                self::XML_PATH_PRICE_ALLOW,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )) {
                continue;
            }

            try {
                /** @var Demka_Alert_Model_Resource_Price_Collection $collection */
                $collection = Mage::getModel('demkaalert/stock')->getCollection();
                $collection->addFieldToFilter('website_id', $website->getId());
                $collection->addFieldToFilter('status', 0);
                $collection->setOrder('customer_email');
            } catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                return $this;
            }

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

                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($website->getDefaultStore()->getId())
                        ->load($alert->getProductId());

                    /* @var $product Mage_Catalog_Model_Product */
                    if (!$product) {
                        continue;
                    }

                    if ($product->isSalable()) {
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
        }
        Mage::app()->setCurrentStore($originalStore);
        return $this;

    }
}
