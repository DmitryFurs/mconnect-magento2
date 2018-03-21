<?php

namespace MalibuCommerce\MConnect\Model\Navision\Invoice;

class Pdf extends \MalibuCommerce\MConnect\Model\Navision\AbstractModel
{
    public function get($invoiceNumber, $customerNumber)
    {
        $xml = new \simpleXMLElement('<sales_invoice />');
        $params = $xml->addChild('parameters');
        $params->invoice_number = $invoiceNumber;
        $params->customer_number = $customerNumber;
        $response = $this->getConnection()->ExportPDF(array(
            'requestXML'  => base64_encode($xml->asXML()),
            'response'    => false,
            'errorLogXML' => false,
        ));
        if (isset($response->response) && strlen($response->response)) {
            $responsePdf = base64_decode($response->response);
            return $responsePdf;
        }
        return false;
    }

}