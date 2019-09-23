<?php

class Demka_Trial_Block_Advertising extends Mage_Core_Block_Template
{
    public function isVisible()
    {
        if ($this->getRequest()->getModuleName() === 'catalog') {
            $currentProduct = Mage::registry('current_product');
            $sku = $currentProduct->getSku();
            return $sku === 'advertising';
        }

        return $this->getRequest()->getModuleName() === 'trial';
    }

    public function getMessage()
    {
        return "Здесь могла быть ваша реклама!";
    }
}