<?php

class Demka_News_Model_Product extends Mage_ImportExport_Model_Export_Entity_Product
{

    protected function _prepareEntityCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        /** @var Mage_Eav_Model_Entity_Collection_Abstract $orig_collection */
        $orig_collection = parent::_prepareEntityCollection($collection);
        $orig_collection->addStaticField('tsg_main_news_id');
        $orig_collection->addStaticField('tsg_main_news_title');
        $orig_collection->addStaticField('tsg_main_news_content');
        $orig_collection->addStaticField('tsg_main_news_image');

        /** @var Demka_News_Model_Resource_News_Collection $newsCollection */
        $newsCollection = Mage::getModel('demkanews/news')->getCollection();
        $newsCollection->load();
        $newsCollectionData = $newsCollection->getData();

        foreach ($orig_collection as $key => $item) {
            $newsId = $item->getData('tsg_main_news');

            if (!empty($newsId)) {
                $filteredNews = array_filter($newsCollectionData, function ($v) use ($newsId) {
                    return $v['id'] === $newsId;
                });
                $newsItem = reset($filteredNews);

                $item->setData('tsg_main_news_id', $newsItem['id']);
                $item->setData('tsg_main_news_title', $newsItem['title']);
                $item->setData('tsg_main_news_content', $newsItem['content']);
                $item->setData('tsg_main_news_image', $newsItem['image']);
            }
        }

        return $orig_collection;
    }

    protected function _getExportAttrCodes()
    {
        $codes = parent::_getExportAttrCodes();
        $codes[] = 'tsg_main_news_id';
        $codes[] = 'tsg_main_news_title';
        $codes[] = 'tsg_main_news_content';
        $codes[] = 'tsg_main_news_image';

        return $codes;
    }
}
