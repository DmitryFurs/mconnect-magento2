<?php
namespace MalibuCommerce\MConnect\Model\Navision;

class Invoice extends AbstractModel
{
    public function export($page = 0, $lastUpdated = false, $websiteId = 0)
    {
        $max = $this->config->get(\MalibuCommerce\MConnect\Model\Queue\Invoice::CODE . '/max_rows');
        $parameters = array(
            'skip'     => $page * $max,
            'max_rows' => $max,
        );
        if ($lastUpdated) {
            $parameters['last_updated'] = $lastUpdated;
        }

        return $this->_export('invoice_export', $parameters, $websiteId);
    }
}