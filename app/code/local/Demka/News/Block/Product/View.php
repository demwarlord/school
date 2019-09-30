<?php

class Demka_News_Block_Product_View extends Mage_Core_Block_Template
{
    public function isVisible()
    {
        return !empty(Mage::registry('current_product')->getData()['tsg_news']);
    }

    public function getLink()
    {
        $currentProductId = Mage::registry('current_product')->getData()['entity_id'];
        $urlParams = [];
        $urlParams['_query'] = ['product_id' => $currentProductId];
        return $this->getUrl('news/', $urlParams);
    }
}