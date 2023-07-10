<?php declare(strict_types=1);

namespace TransmitSecurity\DrsSecurityExtension\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class OrderManagement
{
    protected ScopeConfigInterface $scopeConfig;
    protected EncryptorInterface $encryptor;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }
    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface           $order
     *
     * @return OrderInterface[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePlace(
        OrderManagementInterface $subject,
        OrderInterface $order
    ): array {
        echo 'Handling beforePlace DRS plugin';
        $enableDeny = $this->scopeConfig->getValue('security_extension_section/security_extension_group/enable_deny');
        echo $enableDeny;
        $actionToken = $order->getData("actionToken");
        echo $actionToken;
        if ($actionToken == null || $enableDeny == null || $enableDeny == false) {
            return [$order];
        }
        $response = $this->getRecommendation($actionToken);
        $this->getRecommendation($actionToken);
        if ($response['recommendation']['type'] == 'DENY') {
            echo 'Deny recommendation';
            return [];
       }
        return [$order];
    }

    private function getRecommendation($actionToken) {
        $accessToken = $this->getAccessToken($actionToken);
        $recommendation = 'UNKNOWN';
        if ($accessToken == null) {
            return $recommendation;
        }
        $url = 'https://api.transmitsecurity.io/risk/v1/recommendation?action_token=' . urlencode($action_token);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            $recommendation = $response['recommendation']['type'];
        }
        curl_close($ch);
        return $recommendation;
    }

    private function getAccessToken() {
        $tokenEndpoint = 'https://api.transmitsecurity.io/oidc/token';
        $clientId = $this->scopeConfig->getValue('security_extension_section/security_extension_group/client_id');
        $clientSecretRaw = $this->scopeConfig->getValue('security_extension_section/security_extension_group/client_secret');
        $clientSecret = $this->encryptor->decrypt($clientSecretRaw);

        $params = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
            'resource' => 'https://riskid.identity.security'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
            return null;
        }
        curl_close($ch);
        return $response['access_token'];
    }
}