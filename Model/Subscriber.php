<?php


namespace Infocube\Newsletter\Model;


use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Model\Service\CouponManagementService;


/********************************************************************************
 * Class Subscriber
 * @package Infocube\Newsletter\Model
 *
 * This model adds functionality to the newsletter subscriber confirmation
 * process by modifying the sendConfirmationSuccessEmail method to switch
 * template and add a little coupon code to the resulting email.
 */

class Subscriber extends \Magento\Newsletter\Model\Subscriber
{
    protected $_couponGenerationSpec;
    protected $_couponManager;
    protected $_scopeConfig;
    public function __construct(\Magento\Framework\Model\Context $context,
                                \Magento\Framework\Registry $registry,
                                \Magento\Newsletter\Helper\Data $newsletterData,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Customer\Model\Session $customerSession, CustomerRepositoryInterface $customerRepository,
                                AccountManagementInterface $customerAccountManagement,
                                \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
                                CouponGenerationSpecInterface $couponGenerationSpec,
                                CouponManagementService $couponManagementService,
                                \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
                                \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
                                array $data = [],
                                \Magento\Framework\Stdlib\DateTime\DateTime $dateTime = null,
                                CustomerInterfaceFactory $customerFactory = null,
                                DataObjectHelper $dataObjectHelper = null
                                )
    {
        $this->_couponGenerationSpec = $couponGenerationSpec;
        $this->_couponManager = $couponManagementService;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $newsletterData, $scopeConfig, $transportBuilder, $storeManager, $customerSession, $customerRepository, $customerAccountManagement, $inlineTranslation, $resource, $resourceCollection, $data, $dateTime, $customerFactory, $dataObjectHelper);
    }

    /* This is the function which we want to extend the functionality. We retrieve the rule id from the core_config
       and generate a coupon code which is then appended to the email to be sent. Most of the other commands are from
       the default 'sendConfirmationSuccessEmail' function */
    public function sendConfirmationSuccessEmail()
    {
        $ruleId = $this->_scopeConfig->getValue('infocube/newsletter/subscriber_reward_rule');
        $this->_couponGenerationSpec->setRuleId($ruleId)->setFormat('alphanum')->setDelimiterAtEvery(4)
            ->setQuantity(1)->setPrefix('IN25')->setSuffix('ENDI')->setLength(20);
        $code = $this->_couponManager->generate($this->_couponGenerationSpec);
        $code = $code[0];
        if ($this->getImportMode()) {
            return $this;
        }

        if (!$this->_scopeConfig->getValue(
                self::XML_PATH_SUCCESS_EMAIL_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        ) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $this->_transportBuilder->setTemplateIdentifier('infocube_subscription_confirm_email_template')->setTemplateOptions(
            [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ]
        )->setTemplateVars(
            ['coupon_code' => $code]
        )->setFrom(
            $this->_scopeConfig->getValue(
                self::XML_PATH_SUCCESS_EMAIL_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->addTo(
            $this->getEmail(),
            $this->getName()
        );
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        return $this;
    }
}
