<?php
namespace MalibuCommerce\MConnect\Model\Navision;

class Pricerule extends \MalibuCommerce\MConnect\Model\Navision\AbstractModel
{
    /**
     * @var \MalibuCommerce\MConnect\Model\Config
     */
    protected $config;

    public function __construct(
        \MalibuCommerce\MConnect\Model\Config $config,
        \MalibuCommerce\MConnect\Model\Navision\Connection $mConnectNavisionConnection
    ) {
        $this->config = $config;

        parent::__construct(
            $mConnectNavisionConnection
        );
    }

    public function export($page = 0, $lastUpdated = false)
    {
        $config = $this->config;
        $max = $config->get('pric_erule/max_rows');
        $parameters = array(
            'skip'     => $page * $max,
            'max_rows' => $max,
        );
        if ($lastUpdated) {
            $parameters['last_updated'] = $lastUpdated;
        }

        return $this->_export('sales_price_export', $parameters);
    }
}