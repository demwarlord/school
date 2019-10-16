<?php

abstract class Demka_Alert_Block_Email_Abstract extends Mage_Core_Block_Template
{

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getFilteredProductShortDescription(Mage_Catalog_Model_Product $product): string
    {
        $shortDescription = $product->getShortDescription();
        if ($shortDescription) {
            $shortDescription = Mage::getSingleton('core/input_filter_maliciousCode')->filter($shortDescription);
        }
        return $shortDescription;
    }

    /**
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function getUrlParams(): array
    {
        return array(
            '_store' => Mage::app()->getStore(),
            '_store_to_url' => true
        );
    }
}