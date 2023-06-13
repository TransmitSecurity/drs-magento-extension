<?php
namespace TransmitSecurity\DrsSecurityExtension\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class Index extends Action
{
    protected ScopeConfigInterface $scopeConfig;
    protected JsonFactory $resultJsonFactory;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        JsonFactory $resultJsonFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    /**
     * @return Json
     */
    public function execute(): Json
    {

        $clientId = $this->scopeConfig->getValue('security_extension_section/security_extension_group/client_id');
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['client_id' => $clientId]);
    }
}
