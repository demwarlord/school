<?php

class Demka_News_Block_Adminhtml_News_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $helper = Mage::helper('demkanews');
        $model = Mage::registry('current_news');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array(
                'id' => $this->getRequest()->getParam('id')
            )),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $this->setForm($form);

        $fieldset = $form->addFieldset('news_form', array('legend' => $helper->__('News Information')));

        $fieldset->addField('title', 'text', array(
            'label' => $helper->__('Title'),
            'required' => true,
            'name' => 'title',
        ));

        $fieldset->addField('content', 'editor', array(
            'label' => $helper->__('Content'),
            'required' => true,
            'name' => 'content',
        ));

        $fieldset->addField('image', 'text', array(
            'label' => $helper->__('Image'),
            'required' => false,
            'name' => 'image'
        ));

        $fieldset->addField('priority', 'text', array(
            'label' => $helper->__('Priority'),
            'required' => false,
            'name' => 'priority'
        ));

        $fieldset->addField('created', 'datetime', array(
            'label' => $helper->__('Date'),
            'required' => false,
            'name' => 'created',
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Varien_Date::DATE_INTERNAL_FORMAT,
        ));

        $form->setUseContainer(true);

        if($data = Mage::getSingleton('adminhtml/session')->getFormData()){
            $form->setValues($data);
        } else {
            $form->setValues($model->getData());
        }

        return parent::_prepareForm();
    }

}
