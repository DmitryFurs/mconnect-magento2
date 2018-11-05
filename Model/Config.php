<?php

namespace MalibuCommerce\MConnect\Model;

class Config
{
    const XML_PATH_CONFIG_SECTION        = 'malibucommerce_mconnect';
    const DEFAULT_NAV_CONNECTION_TIMEOUT = 10; // in seconds
    const DEFAULT_NEW_ORDERS_DELAY       = 5; // in minutes

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Registry                        $registry
     * @param \Magento\Framework\Module\Manager                  $moduleManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->getFlag('general/enabled');
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return string
     */
    public function getNavConnectionUrl($websiteId = null)
    {
        return $this->getWebsiteData('nav_connection/url', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return string
     */
    public function getNavConnectionUsername($websiteId = null)
    {
        return $this->getWebsiteData('nav_connection/username', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return string
     */
    public function getNavConnectionPassword($websiteId = null)
    {
        return $this->getWebsiteData('nav_connection/password', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return bool
     */
    public function getUseNtlmAuthentication($websiteId = null)
    {
        return (bool)$this->getWebsiteData('nav_connection/ntlm', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return bool
     */
    public function getIsInsecureConnectionAllowed($websiteId = null)
    {
        return (bool)$this->getWebsiteData('nav_connection/allow_insecure_connection', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return int
     */
    public function getConnectionTimeout($websiteId = null)
    {
        $timeout = (int)$this->getWebsiteData('nav_connection/connection_timeout', $websiteId);
        if ($timeout <= 0) {
            return self::DEFAULT_NAV_CONNECTION_TIMEOUT;
        }

        return $timeout;
    }

    /**
     * Get decrypted trigger password
     *
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return string
     */
    public function getTriggerPassword($websiteId = null)
    {
        return $this->getWebsiteData('nav_connection/trigger_password', $websiteId);
    }

    /**
     * When Signifyd Integration is enabled, it will put any fraudulent orders on hold
     * but that happens asynchronously to how order are being placed into the Mconnect queue for NAV export.
     *
     * Thus we need to delay orders export forcefully, so that until Magento_Signifyd puts new orders on hold
     * they are not being accidentally exported to NAV yet.
     *
     * When forced order export delay is activated but Mconnect "Hold New Orders Export" is not enabled,
     * there will be a corresponding message next to the config field "Hold New Orders Export" in Admin Panel.
     *
     * Note: older Magento versions are not shipped with Magento_Signifyd
     *
     * @return bool
     */
    public function shouldNewOrdersBeForcefullyHolden()
    {
        if ($this->moduleManager->isEnabled('Magento_Signifyd')) {
            $enabled = $this->scopeConfig->isSetFlag(
                'fraud_protection/signifyd/active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            return $enabled;
        }

        return false;
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return bool
     */
    public function getIsHoldNewOrdersExport($websiteId = null)
    {
        return (bool)$this->getWebsiteData('order/hold_new_orders_export', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return int
     */
    public function getHoldNewOrdersDelay($websiteId = null)
    {
        $delay = (int)$this->getWebsiteData('order/hold_new_orders_delay', $websiteId);

        if (!$this->getIsHoldNewOrdersExport($websiteId) && $this->shouldNewOrdersBeForcefullyHolden()) {
            $delay = self::DEFAULT_NEW_ORDERS_DELAY;
        }

        return $delay;
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return string
     */
    public function getOrderStatusWhenSyncedToNav($websiteId = null)
    {
        return $this->getWebsiteData('order/order_status_when_synced_to_nav', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return array
     */
    public function getOrderStatuesAllowedForExportToNav($websiteId = null)
    {
        return explode(',', $this->getWebsiteData('order/allowed_order_statuses_to_export', $websiteId));
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return array
     */
    public function getNAVReportsCustomerGroups($websiteId = null)
    {
        return explode(',', $this->getWebsiteData('customer/nav_reports_allowed_customer_groups', $websiteId));
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return bool
     */
    public function isErrorEmailingEnabled($websiteId = null)
    {
        return (bool)$this->getWebsiteData('nav_connection/send_error_emails', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return string
     */
    public function getErrorEmailSender($websiteId = null)
    {
        return $this->getWebsiteData('nav_connection/error_email_sender', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return array
     */
    public function getErrorRecipients($websiteId = null)
    {
        return array_map('trim', explode(',', $this->getWebsiteData('nav_connection/error_email_recipient', $websiteId)));
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return string
     */
    public function getErrorEmailTemplate($websiteId = null)
    {
        return $this->getWebsiteData('nav_connection/error_email_template', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return bool
     */
    public function isNewCustomerPasswordResetEmailingEnabled($websiteId = null)
    {
        return (bool)$this->getWebsiteData('customer/send_new_customer_emails', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return string
     */
    public function getNewCustomerPasswordResetEmailSender($websiteId = null)
    {
        return $this->getWebsiteData('customer/new_customer_email_sender', $websiteId);
    }

    /**
     * @param null|int|string|\Magento\Store\Model\Website $websiteId
     *
     * @return string
     */
    public function getNewCustomerPasswordResetEmailTemplate($websiteId = null)
    {
        return $this->getWebsiteData('customer/new_customer_email_template', $websiteId);
    }

    public function getFlag($path, $store = null)
    {
        return boolval($this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_SECTION . '/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    public function get($path, $store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_SECTION . '/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getWebsiteData($path, $website = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_SECTION . '/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $website
        );
    }
}
