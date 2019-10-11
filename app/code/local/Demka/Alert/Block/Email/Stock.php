<?php

class Demka_Alert_Block_Email_Stock extends Mage_Core_Block_Template
{

    public function _getFilteredProductShortDescription(Mage_Catalog_Model_Product $product)
    {
        $shortDescription = $product->getShortDescription();
        if ($shortDescription) {
            $shortDescription = Mage::getSingleton('core/input_filter_maliciousCode')->filter($shortDescription);
        }
        return $shortDescription;
    }

    public function getProductUnsubscribeUrl($productId, $customerEmail)
    {
        $params = $this->_getUrlParams();
        $params['product'] = $productId;
        $params['email'] = $customerEmail;
        return $this->getUrl('alert/unsubscribe/stock', $params);
    }

    protected function _getUrlParams()
    {
        return array(
            '_store' => Mage::app()->getStore(),
            '_store_to_url' => true
        );
    }

    public function getUnsubscribeUrl($customerEmail)
    {
        $params = $this->_getUrlParams();
        $params['email'] = $customerEmail;
        return $this->getUrl('alert/unsubscribe/stockAll', $params);
    }
}