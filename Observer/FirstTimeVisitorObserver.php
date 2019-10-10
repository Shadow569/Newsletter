<?php

/********************************************************************
 * This Observer listens to the event 'visitor_ini' and sets the
 * internal variable firstTimeVisitor to true. This observer is
 * passed to the observer that listens to the even 'load_layout_before'
 * and makes a choice to add a newsletter registration modal or no
 * based on the value of this internal flag.
 */
namespace Infocube\Newsletter\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class FirstTimeVisitorObserver implements ObserverInterface
{
    /* This is the flag that helps other observers determine if the visitor is a new user */
    protected $_firstTimeVisitor;

    /* This observer is tied to the 'visitor_init' event. Which means that a new visitor came in. So it sets the respective flag */
    public function execute(Observer $observer)
    {
        $this->_firstTimeVisitor = true;
    }

    /**********************************************************************************
     * @return bool
     *
     * This method is to be used by other classes or procedures to check if a visitor
     * has just came to the store.
     */
    public function getFirstTimeVisitor(){
        if(isset($this->_firstTimeVisitor)){
            return true;
        }
        else{
            return false;
        }
    }
}
