<?php


namespace Infocube\Newsletter\Block;


/***************************************************************************
 * Class FirstTimeVisitor
 * @package Infocube\Newsletter\Block
 *
 * This is the block which holds data to be rendered to the newsletter form
 * modal. This will have the functionality of adjusting the price of the
 * reward coupon based on configuration options made by the administration
 * or store owner. For now this just renders some hardcoded values.
 */

class FirstTimeVisitor extends \Magento\Framework\View\Element\Template
{

    public function getCouponAmount(){
        return '5';
    }

    public function getStoreCurrency(){
        return '€';
    }
}
