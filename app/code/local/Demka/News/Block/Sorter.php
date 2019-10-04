<?php

class Demka_News_Block_Sorter extends Mage_Core_Block_Template
{
    protected $_orderVarName = 'order';
    protected $_directionVarName = 'dir';
    protected $_orderField = null;
    protected $_direction = 'asc';
    protected $_availableOrder = array();

    protected function _construct()
    {
        parent::_construct();
        $this->_orderField = 'created';
        $this->_availableOrder = ['created' => 'Дата новости', 'title' => 'Заголовок'];
        $this->setTemplate('demkanews/sorter.phtml');
    }

    public function getAvailableOrders()
    {
        return $this->_availableOrder;
    }

    public function setAvailableOrders($orders)
    {
        $this->_availableOrder = $orders;
        return $this;
    }

    public function getOrderVarName()
    {
        return $this->_orderVarName;
    }

    public function getDirectionVarName()
    {
        return $this->_directionVarName;
    }

    public function setDefaultDirection($dir)
    {
        if (in_array(strtolower($dir), array('asc', 'desc'))) {
            $this->_direction = strtolower($dir);
        }
        return $this;
    }

    public function getCurrentOrder()
    {
        $order = $this->_getData('current_sorter_order');
        if ($order) {
            return $order;
        }

        $orders = $this->getAvailableOrders();
        $defaultOrder = $this->_orderField;

        if (!isset($orders[$defaultOrder])) {
            $keys = array_keys($orders);
            $defaultOrder = $keys[0];
        }

        $order = $this->getRequest()->getParam($this->getOrderVarName());
        if ($order && isset($orders[$order])) {
            if ($order == $defaultOrder) {
                Mage::getSingleton('core/session')->unsSortOrder();
            } else {
                $this->_memorizeParam('sort_order', $order);
            }
        } else {
            $order = Mage::getSingleton('core/session')->getSortOrder();
        }
        // validate session value
        if (!$order || !isset($orders[$order])) {
            $order = $defaultOrder;
        }
        $this->setData('current_sorter_order', $order);
        return $order;
    }

    public function getCurrentDirection()
    {
        $dir = $this->_getData('current_sorter_direction');
        if ($dir) {
            return $dir;
        }

        $directions = array('asc', 'desc');
        $dir = strtolower($this->getRequest()->getParam($this->getDirectionVarName()));
        if ($dir && in_array($dir, $directions)) {
            if ($dir == $this->_direction) {
                Mage::getSingleton('core/session')->unsSortDirection();
            } else {
                $this->_memorizeParam('sort_direction', $dir);
            }
        } else {
            $dir = Mage::getSingleton('core/session')->getSortDirection();
        }
        // validate direction
        if (!$dir || !in_array($dir, $directions)) {
            $dir = $this->_direction;
        }
        $this->setData('current_sorter_direction', $dir);
        return $dir;
    }

    public function getOrderUrl($order, $direction)
    {
        if (is_null($order)) {
            $order = $this->getCurrentOrder() ? $this->getCurrentOrder() : $this->_availableOrder[0];
        }
        return $this->getPagerUrl(array(
            $this->getOrderVarName() => $order,
            $this->getDirectionVarName() => $direction,
        ));
    }

    public function getPagerUrl($params = array())
    {
        $urlParams = array();
        $urlParams['_current'] = true;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }

    public function isOrderCurrent($order)
    {
        return ($order == $this->getCurrentOrder());
    }

    protected function _memorizeParam($param, $value)
    {
        $session = Mage::getSingleton('core/session');
        $session->setData($param, $value);
        return $this;
    }
}