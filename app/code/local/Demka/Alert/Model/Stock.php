<?php

class Demka_Alert_Model_Stock extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('demkaalert/stock');
    }
}