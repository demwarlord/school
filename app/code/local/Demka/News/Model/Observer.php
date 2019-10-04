<?php

class Demka_News_Model_Observer
{
    public function catalogProductPrepareSave(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getData()['product'];
        $data = $product->getData();
        $newSelectedNews = $data['tsg_news'] ?? [];
        $newSelectedMainNews = !empty($data['tsg_main_news'])
        && in_array($data['tsg_main_news'], $newSelectedNews) ? (int)$data['tsg_main_news'] : 0;

        if (!empty($newSelectedNews)) {
            if (empty($newSelectedMainNews)) {
                $product->setData('tsg_main_news', $this->_selectMainNewsByPriority($newSelectedNews));
            }
        } else {
            $product->setData('tsg_main_news', 0);
        }
    }

    private function _selectMainNewsByPriority($selectedNews)
    {
        /** @var Demka_News_Model_Resource_News_Collection $collection */
        $collection = Mage::getModel('demkanews/news')->getCollection();
        $newsList = $collection->addFieldToFilter('id', $selectedNews)->load()->getData();

        $newsSelectToMain = array_reduce($newsList, function ($carry, $item) {
            $itemPriority = $item['priority'] * 86400 + strtotime($item['created']);
            return (empty($carry) || ($carry['priority'] * 86400 + strtotime($carry['created'])) < $itemPriority ? $item : $carry);
        });
        return $newsSelectToMain['id'] ?? 0;
    }
}