<?php
namespace drs\SecurityExtension\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
class Index extends Action
{
    protected ScopeConfigInterface $scopeConfig;
    protected JsonFactory $resultJsonFactory;

    protected $customerSession;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }
    /**
     * @return Json
     */
    public function execute(): Json
    {
        $customerId = null;
        if($this->customerSession->getCustomer() !== null){
            $customerId = $this->customerSession->getCustomer()->getId();
        }
        $clientId = $this->scopeConfig->getValue('security_extension_section/security_extension_group/client_id');
        $clientSec = $this->scopeConfig->getValue('security_extension_section/security_extension_group/client_secret');
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['client_id' => $clientId, 'client_secret' => $clientSec, 'customer_id' => $customerId]);
    }
}
