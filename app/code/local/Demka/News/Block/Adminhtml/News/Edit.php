<?php

class Demka_News_Block_Adminhtml_News_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'demkanews';
        $this->_controller = 'adminhtml_news';
    }

    public function getHeaderText()
    {
        $helper = Mage::helper('demkanews');
        $model = Mage::registry('current_news');

        if ($model->getId()) {
            return $helper->__("Edit News Item '%s'", $this->escapeHtml($model->getTitle()));
        } else {
            return $helper->__("Add News Item");
        }
    }
}