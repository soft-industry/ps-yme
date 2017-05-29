<?php
/**
 * 2017 Soft Industry
 *
 *   @author    Skorobogatko Alexei <a.skorobogatko@soft-industry.com>
 *   @copyright 2017 Soft-Industry
 *   @license   http://opensource.org/licenses/afl-3.0.php
 *   @since     0.1.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use SI\YandexMarket\Generator;
use SI\YandexMarket\OfferType;
use SI\YandexMarket\Renders\Factory as RenderFactory;

/**
 * Generate YML data.
 *
 * Route is /module/yme/export
 *
 * @author skoro
 */
class YmeExportModuleFrontController extends ModuleFrontController
{
    
    /**
     * @inheritdoc
     */
    public function postProcess()
    {
        if (!$this->module->isExportEnabled()) {
            die();
        }
        
        $generator = $this->createGenerator();
        $filename = $this->getFilename($generator);

        header('Cache-Control: no-store, no-cache');

        switch ($this->module->getExportFormat()) {
            case RenderFactory::YML:
                header('Content-Type: application/xml');
                break;
            
            case RenderFactory::CSV:
                header('Content-Type: text/csv');
                header('Content-Type: application/force-download; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                break;
            
            case RenderFactory::XLS:
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                break;
        }
        
        echo $generator->getRender()->getHeader();
        echo $generator->processProducts();
        echo $generator->getRender()->getFooter();
        
        die();
    }
    
    /**
     * @return Generator
     */
    protected function createGenerator()
    {
        return new Generator($this->createOfferType(), $this->createRender());
    }
    
    /**
     * @return Render
     */
    protected function createRender()
    {
        $format = $this->module->getExportFormat();

        $options = [];
        
        if ($format === RenderFactory::YML) {
            $options['company']    = Configuration::get('BLOCKCONTACTINFOS_COMPANY');
            $options['shop_name']  = Configuration::get('PS_SHOP_NAME');
            $options['shop_url']   = $this->getShopUrl();
            $options['currencies'] = [$this, 'getCurrencies'];
            $options['categories'] = [$this, 'getCategories'];
        }

        return RenderFactory::create($format, $options);
    }
    
    /**
     * @return OfferType
     */
    protected function createOfferType()
    {
        $offer_type = $this->module->getOfferType();
        $features = array_filter($this->module->getOfferFeatures($offer_type, null, true));
        return new OfferType($offer_type, $features);
    }
    
    /**
     * Gets export filename.
     *
     * @param Generator $generator
     * @return string
     */
    protected function getFilename(Generator $generator, $base = 'export-')
    {
        return sprintf('%s%s-%s.%s',
            $base,
            $generator->getOfferType()->getType(),
            date('Y-m-d'),
            strtolower($this->module->getExportFormat())
        );
    }
    
    /**
     * Returns absolute shop url.
     *
     * @return string
     */
    protected function getShopUrl()
    {
        $ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $shop = Context::getContext()->shop;
        return ($ssl ? 'https://' . $shop->domain_ssl : 'http://' . $shop->domain)
                . $shop->getBaseURI();
    }
    
    /**
     * Returns list of currencies suitable for YML render.
     *
     * @return array
     */
    public function getCurrencies()
    {
        return array_map(function (Currency $currency) {
            return [
                'id'   => $currency->iso_code,
                'rate' => $currency->conversion_rate,
            ];
        }, Currency::getCurrencies(true));
    }
    
    /**
     * Return list of categories suitable for YML render.
     *
     * @return array
     */
    public function getCategories()
    {
        $result = [];
        $categories = Category::getCategories(Context::getContext()->language->id);

        foreach ($categories as $children) {

            foreach ($children as $child) {
                $child = $child['infos'];
                $result[] = [
                    'id'       => $child['id_category'],
                    'name'     => $child['name'],
                    'parentId' => $child['id_parent'],
                ];
            }

        }
        
        return $result;
    }
}
