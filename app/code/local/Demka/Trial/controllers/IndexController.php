<?php

class Demka_Trial_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function advertisingAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}