<?php

class Demka_News_Block_News extends Mage_Page_Block_Html_Pager
{
    public function _construct()
    {
        /** @var Demka_News_Model_Resource_News_Collection $collection */
        $collection = Mage::getModel('demkanews/news')->getCollection();
        $this->setCollection($collection);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var Mage_Page_Block_Html_Pager $pager */
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(2 => 2, 10 => 10, 20 => 20, 'all' => 'all'));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);

        /** @var Demka_News_Block_Sorter $sorter */
        $sorter = $this->getLayout()->createBlock('demkanews/sorter', 'custom.sorter');
        $this->setChild('sorter', $sorter);

        $dir = $sorter->getCurrentDirection();
        $orderBy = $sorter->getCurrentOrder();

        $this->getCollection()->addOrder($orderBy, $dir)->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getSorterHtml()
    {
        return $this->getChildHtml('sorter');
    }

    public function getNewsCollection()
    {
        return $this->getCollection();
    }

    public function truncateNewsItem($text, $length = 100)
    {
        return mb_substr($this->stripTags($text), 0, $length);
    }

    public function formatDate($date = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showTime = false)
    {
        return parent::formatDate($date, $format, $showTime);
    }
}
