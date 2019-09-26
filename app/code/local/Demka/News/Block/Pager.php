<?php

class Demka_News_Block_Pager extends Mage_Page_Block_Html_Pager
{
    protected function _construct()
    {
        parent::_construct();

        $this->setAvailableLimit(Mage::getModel('demkanews/paging')->getAvailablePagingOptions());
    }

    public function getLimit()
    {
        if ($this->_limit !== null) {
            return $this->_limit;
        }
        $limits = $this->getAvailableLimit();
        if ($limit = $this->getRequest()->getParam($this->getLimitVarName())) {
            if (isset($limits[$limit])) {
                return $limit;
            }
        }

        return Mage::getStoreConfig('demkanews_options/demkanews_group/demkanews_news_number');
    }
}