<?php

class Demka_News_Model_Attribute_Source_Tsgnews extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    public function getAllOptions()
    {
        $this->_options = [];
        /** @var Demka_News_Model_Resource_News_Collection $collection */
        $collection = Mage::getModel('demkanews/news')->getCollection();
        $newsList = $collection->load()->getData();

        foreach ($newsList as $newsItem) {
            $this->_options[] = [
                'label' => "["
                    . $newsItem['priority']
                    . "] ["
                    . Mage::helper('core')->formatDate($newsItem['created'])
                    . "] "
                    . $newsItem['title'],
                'value' => $newsItem['id']
            ];
        }

        return $this->_options;
    }
}