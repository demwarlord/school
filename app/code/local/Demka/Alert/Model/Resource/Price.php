<?php

class Demka_Alert_Model_Resource_Price extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('demkaalert/table_alert_price', 'alert_price_id');
    }
}