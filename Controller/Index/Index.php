<?php


namespace Infocube\Newsletter\Controller\Index;



use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Newsletter\Model\ResourceModel\Subscriber;


/***************************************************************************
 * Class Index
 * @package Infocube\Newsletter\Controller\Index
 *
 * This controller class receives an AJAX request from the newsletter
 * registration form and subscribes the provided email to the newsletter.
 */
class Index extends \Magento\Framework\App\Action\Action
{
    protected $_resultJsonFactory;
    protected $_newsLetterManager;
    protected $_subscriberModel;
    protected $_email;
    protected $_storeManager;


    /*************************************************************************************************************
     * Index constructor.
     * @param Context $context
     * @param JsonFactory $resultFactory
     * @param Subscriber $_newsLetterResource
     * @param \Magento\Newsletter\Model\Subscriber $subscriberModel
     * @param Request $request
     *
     * The constructor injects classes and request and integrates them into
     * the class attributes
     */
    public function __construct(Context $context, JsonFactory $resultFactory, Subscriber $_newsLetterResource,
                                \Magento\Newsletter\Model\Subscriber $subscriberModel, Request $request)
    {
        $this->_resultJsonFactory = $resultFactory;
        $this->_newsLetterManager = $_newsLetterResource;
        $this->_email = $request->getPost('email');
        $this->_subscriberModel = $subscriberModel;
        parent::__construct($context);
    }

    /*************************************************************************************************************
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     *
     * This executes the action of registering the provided Email to the newsletter. After successful registration
     * the controller sets a certain flag to let the other end point know that everything went well. If it is not,
     * the controller sets another flag to let the other end point know that something went wrong.
     */
    public function execute()
    {
        $jsonData = $this->_resultJsonFactory->create(['status'=>'standby']);
        try {
            $this->_subscriberModel->subscribe($this->_email);
            $this->_subscriberModel->loadByEmail($this->_email);
            if($this->_subscriberModel->getStatus() == $this->_subscriberModel::STATUS_NOT_ACTIVE)//Checks if user was subscribed successfully.
            {
                $jsonData->setData(['status'=>'subscriber_saved_confiramtion_required']);
            } else {
                $jsonData->setData(['status'=>'failed']);
            }
        }
        catch(\Exception $e){
            $jsonData->setData(['status'=>'exception','except_message'=>$e->getMessage()]);
        }
        return $jsonData;
    }
}
