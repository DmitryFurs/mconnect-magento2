<?php

namespace MalibuCommerce\MConnect\Ui\Component\Listing\Column;

class Entity extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customerCustomer;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $catalogProduct;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Customer\Model\Customer $customerCustomer,
        \Magento\Catalog\Model\Product $catalogProduct,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->customerCustomer = $customerCustomer;
        $this->catalogProduct = $catalogProduct;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (!empty($item['entity_id'])) {
                    $link = false;
                    $title = false;
                    if ($item['code'] === 'customer') {
                        if ($item['action'] === 'export') {
                            $link = $this->urlBuilder->getUrl('customer/index/edit', array('id' => $item['entity_id']));
                            $title = $this->customerCustomer->load($item['entity_id'])->getEmail();
                        }
                    }
                    if ($item['code'] === 'product') {
                        if ($item['action'] === 'import_single') {
                            $link = $this->urlBuilder->getUrl('catalog/product/edit', array('id' => $item['entity_id']));
                            $title = $this->catalogProduct->load($item['entity_id'])->getName();
                        }
                    }
                    if ($link !== false) {
                        $item['entity_id'] = sprintf('<a href="%s" target="_blank" title="%s">%s<a/>', $link, $title ? $title : $item['entity_id'], $item['entity_id']);
                    }
                }
            }
        }

        return $dataSource;
    }
}