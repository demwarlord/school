<?php

class Demka_News_Model_Paging extends Mage_Core_Model_Abstract
{
    protected $_avaiablePagingOptions = [
        2 => 2,
        10 => 10,
        20 => 20,
        50 => 50,
        'all' => 'all'
    ];

    public function getAvailablePagingOptions()
    {
        return $this->_avaiablePagingOptions;
    }

    public function toOptionArray()
    {
        return array_map(function ($k, $v) {
            return ['value' => $k, 'label' => $v];
        }, array_keys($this->_avaiablePagingOptions), array_values($this->_avaiablePagingOptions));
    }
}