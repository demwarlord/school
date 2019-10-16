<?php

class Demka_Alert_Block_Email_Stock extends Demka_Alert_Block_Email_Abstract
{
    /**
     * @param string $productId
     * @param string $customerEmail
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getProductUnsubscribeUrl(string $productId, string $customerEmail): string
    {
        $params = $this->getUrlParams();
        $params['product'] = $productId;
        $params['email'] = $customerEmail;
        return $this->getUrl('alert/unsubscribe/stock', $params);
    }

    /**
     * @param string $customerEmail
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getUnsubscribeUrl(string $customerEmail): string
    {
        $params = $this->getUrlParams();
        $params['email'] = $customerEmail;
        return $this->getUrl('alert/unsubscribe/stockAll', $params);
    }
}