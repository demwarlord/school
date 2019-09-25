<?php

class Demka_News_Block_News extends Mage_Page_Block_Html_Pager
{
    public function _construct()
    {
        $collection = Mage::getModel('demkanews/news')->getCollection();
        $this->setCollection($collection);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(2 => 2, 10 => 10, 20 => 20, 'all' => 'all'));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getNewsCollection()
    {
        return $this->getCollection();
    }

    public function truncateNewsItem($text, $length = 100)
    {
        return mb_substr($this->stripTags($text), 0, $length);
    }
}
