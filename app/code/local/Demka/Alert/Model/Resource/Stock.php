<?php

class Demka_Alert_Model_Resource_Stock extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('demkaalert/table_alert_stock', 'alert_stock_id');
    }
}