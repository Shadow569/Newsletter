<?php


namespace Infocube\Newsletter\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


/***************************************************************************************************
 * Class FormCreator
 * @package Infocube\Newsletter\Observer
 *
 * This listens to the event 'load_layout_before' and if the current user is a new visitor, then it
 * appends the required templates to display a newsletter registration form.
 */
class FormCreator implements ObserverInterface
{
    protected $_firstTimeVisitorObserver;

    /* This injects the FirstTimeVisitorObserver to this class to consume the state of the current visitor */
    public function __construct(\Infocube\Newsletter\Observer\FirstTimeVisitorObserver $observer)
    {
        $this->_firstTimeVisitorObserver = $observer;
    }


    /* This is executed when 'load_layout_before' is emitted and checks if the visitor is a new visitor
        and if it is, it will update the layout with a modal newsletter registration form. */
    public function execute(Observer $observer)
    {

        if ($this->_firstTimeVisitorObserver->getFirstTimeVisitor()) {
            $layout = $observer->getEvent()->getLayout();
            $layout->getUpdate()->addHandle('first_time_visitor');
        }
    }
}
