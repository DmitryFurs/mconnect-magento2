<?php

namespace MalibuCommerce\MConnect\Plugin;

/**
 * Used to override product price on PLP
 */
class ProductPlugin
{
    /**
     * @var \MalibuCommerce\MConnect\Model\Pricerule
     */
    protected $rule;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \MalibuCommerce\MConnect\Model\Queue\Promotion
     */
    protected $promotion;

    /**
     * Product plugin constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \MalibuCommerce\MConnect\Model\Pricerule $rule
     * @param \MalibuCommerce\MConnect\Model\Queue\Promotion $promotion
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \MalibuCommerce\MConnect\Model\Pricerule $rule,
        \MalibuCommerce\MConnect\Model\Queue\Promotion $promotion
    ) {
        $this->logger = $logger;
        $this->rule = $rule;
        $this->promotion = $promotion;
    }

    /**
     * Plugin to apply MConnect Price Rules for Product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param $originalFinalPrice
     * @return mixed|null
     */
    public function afterGetPrice(\Magento\Catalog\Model\Product $product, $originalFinalPrice)
    {
        $finalPrice = null;
        try {
            $promoPrice = $this->promotion->getPromoPrice($product);
            echo $product->getSku(); echo '+'; echo $promoPrice; die;
            if($promoPrice != false)  {
                $finalPrice = $promoPrice;
            } else {
                $mconnectPrice = $this->rule->matchDiscountPrice($product, $product->getQty(), $product->getStore()->getWebsiteId());
                if ($mconnectPrice === false) {

                    return $originalFinalPrice;
                }

                if (!$product->hasData('final_price') || $mconnectPrice < $product->getData('final_price')) {
                    $finalPrice = $mconnectPrice;
                }
            }
        } catch (\Throwable $e) {
            $this->logger->critical($e);

            return $originalFinalPrice;
        }

        if (is_null($finalPrice)) {

            return $originalFinalPrice;
        }

        return $finalPrice;
    }
}