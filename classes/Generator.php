<?php
/**
 * 2017 Soft Industry
 *
 *   @author    Skorobogatko Alexei <a.skorobogatko@soft-industry.com>
 *   @copyright 2017 Soft-Industry
 *   @license   http://opensource.org/licenses/afl-3.0.php
 *   @since     0.1.0
 */

namespace SI\YandexMarket;

use Product;
use SI\YandexMarket\Renders\Render;

/**
 * Export generator.
 *
 * @author skoro
 */
class Generator
{
    
    /**
     * @var OfferType
     */
    protected $offerType;
    
    /**
     * @var Render
     */
    protected $render;
    
    /**
     * Creates a new generator.
     *
     * @param OfferType $offerType
     * @param Render $render
     */
    public function __construct(OfferType $offerType, Render $render)
    {
        $this->offerType = $offerType;
        $this->render = $render;
    }
    
    /**
     * @return Render
     */
    public function getRender()
    {
        return $this->render;
    }
    
    /**
     * @return OfferType
     */
    public function getOfferType()
    {
        return $this->offerType;
    }
    
    /**
     * Get product list.
     *
     * @return Product[]
     */
    public function getProducts()
    {
        return Product::getProducts(\Context::getContext()->language->id, 0, 0, 'id_product', 'ASC');
    }
    
    /**
     * Process products and get rendered content.
     *
     * @return string
     */
    public function processProducts()
    {
        $products = $this->getProducts();
        foreach ($products as $info) {
            $product = new Product($info['id_product']);
            if (\Validate::isLoadedObject($product)) {
                $element = $this->offerType->createElement($product);
                if ($element->isValid()) {
                    $this->render->renderElement($element);
                } else {
                    // TODO: log.
                }
            } else {
                // TODO: log.
            }
        }
        return $this->render->flush();
    }
}
