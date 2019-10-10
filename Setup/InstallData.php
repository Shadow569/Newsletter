<?php


namespace Infocube\Newsletter\Setup;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager\ConfigWriterInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\SalesRule\Api\Data\ConditionInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Model\RuleRepository;
use Magento\Store\Model\StoreManagerInterface;


/*******************************************************************************
 * Class InstallData
 * @package Infocube\Newsletter\Setup
 *
 * This Package installs the required data for Infocube Newsletter to be
 * functional and specifically creates a cart price rule which will be
 * always associated with the newsletter reward scheme.
 */

class InstallData implements InstallDataInterface
{
    protected $_cartRuleManager;
    protected $_scopeConfig;
    protected $_storeManager;
    protected $_salesRuleResource;
    protected $_scopeWriter;
    protected $_conditionInf;

    /**************************************************************
     * InstallData constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param RuleInterface $salesRule
     * @param RuleRepository $ruleRepo
     * @param StoreManagerInterface $store
     * @param ConfigWriterInterface $scopeWrite
     *
     * This function injects the required Magento Classes.
     */
    public function __construct(ScopeConfigInterface $scopeConfig,
                                RuleInterface $salesRule,
                                RuleRepository $ruleRepo,
                                StoreManagerInterface $store,
                                ConfigWriterInterface $scopeWrite,
                                ConditionInterface $condinf)
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_cartRuleManager = $salesRule;
        $this->_storeManager = $store;
        $this->_salesRuleResource = $ruleRepo;
        $this->_scopeWriter = $scopeWrite;
        $this->_conditionInf = $condinf;
    }


    /****************************************************************************************************************
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * This creates the cart price rule and saves the id into the core_config_data table
     * under the path 'infocube/newsletter/subscriber_reward_rule'
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->_conditionInf->setConditionType('Magento\SalesRule\Model\Rule\Condition\Address')->setAttributeName('base_subtotal')->setOperator("==")
            ->setValue(30);
        $this->_cartRuleManager->setName('INRE2520')->setFromDate(date('Y-m-d'))->setToDate(NULL)
            ->setUsesPerCustomer(1)->setUsesPerCoupon(1)->setUseAutoGeneration(1)->setCustomerGroupIds(array('0','1','2','3',))
            ->setSimpleAction('by_fixed')->setDiscountAmount(5.0)->setDiscountQty(NULL)->setDiscountStep(NULL)->setApplyToShipping(0)
            ->setIsRss(0)->setWebsiteIds(array($this->_storeManager->getWebsite()->getId()))->setCouponType(1)->setCondition($this->_conditionInf);
        $savedRule = $this->_salesRuleResource->save($this->_cartRuleManager)->getRuleId();
        $this->_scopeWriter->save('infocube/newsletter/subscriber_reward_rule',$savedRule,ScopeConfigInterface::SCOPE_TYPE_DEFAULT,$this->_storeManager->getWebsite()->getId());

    }
}
