<?php

class Demka_News_Model_Attribute_Source_Tsgnewsmain extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    public function getAllOptions()
    {
        $this->_options = [
            [
                'label' => 'Select Main News',
                'value' => 0
            ]
        ];
        $selectedNews = Mage::registry('current_product')->getData()['tsg_news'] ?? false;

        /** @var Demka_News_Model_Resource_News_Collection $collection */
        $collection = Mage::getModel('demkanews/news')->getCollection();

        if (!empty($selectedNews)) {
            $newsList = $collection->addFieldToFilter('id', explode(',', $selectedNews))->load()->getData();
        } else {
            $newsList = $collection->load()->getData();
        }

        foreach ($newsList as $newsItem) {
            $this->_options[] = [
                'label' => $newsItem['title'],
                'value' => $newsItem['id']
            ];
        }

        return $this->_options;
    }
}