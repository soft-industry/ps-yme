<?php
/**
 * 2017 Soft Industry
 *
 *   @author    Skorobogatko Alexei <a.skorobogatko@soft-industry.com>
 *   @copyright 2017 Soft-Industry
 *   @license   http://opensource.org/licenses/afl-3.0.php
 *   @since     0.1.0
 */

namespace SI\YandexMarket\Renders;

use SI\YandexMarket\Elements\Common;
use SI\YandexMarket\OfferType;
use Tools;

/**
 * CSV render.
 *
 * @link https://yandex.ru/support/partnermarket/export/text-format.html
 * @author skoro
 */
class CSV extends Render
{
    
    /**
     * @var array Current row.
     */
    protected $row = [];
    
    /**
     * @var array Added rows.
     */
    protected $rows = [];
    
    /**
     * @inheritdoc
     */
    public function getDefaultOptions()
    {
        return [
            'delimiter' => chr(9), // TAB character.
            'stream' => 'php://output',
        ];
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $handle = fopen($this->options['stream'], 'w');
        if ($handle === false) {
            throw new Exception("Couldn't open stream: " . $this->options['stream']);
        }
        
        fputcsv($handle, $this->getHeaderColumns(), $this->options['delimiter']);
        
        foreach ($this->rows as $row) {
            fputcsv($handle, $row, $this->options['delimiter']);
        }
        
        fclose($handle);
    }

    /**
     * @inheritdoc
     */
    public function renderElement(Common $element)
    {
        $this->row = [];

        $this->setRow('id', $element);
        $this->setRow('available', $element);

        switch ($element->getType()) {
            case OfferType::SIMPLE:
                $this->setRow('name', $element);
                $this->setRow('model', $element);
                $this->setRow('vendor', $element);
                $this->setRow('vendorCode', $element);
                break;
            
            case OfferType::ARBITRARY:
                $this->setRow('model', $element);
                $this->setRow('vendor', $element);
                $this->setRow('vendorCode', $element);
                $this->setRow('typePrefix', $element);
                break;
            
            case OfferType::BOOK:
                $this->setRow('name', $element);
                $this->setRow('author', $element);
                $this->setRow('publisher', $element);
                $this->setRow('series', $element);
                $this->setRow('year', $element);
                $this->setRow('isbn', $element);
                $this->setRow('volume', $element);
                $this->setRow('part', $element);
                $this->setRow('language', $element);
                $this->setRow('table_of_contents', $element);
                $this->setRow('type', $element);
                $this->setRow('binding', $element);
                $this->setRow('page_extent', $element);
                break;
            
            case OfferType::AUDIOBOOK:
                $this->setRow('name', $element);
                $this->setRow('author', $element);
                $this->setRow('publisher', $element);
                $this->setRow('series', $element);
                $this->setRow('year', $element);
                $this->setRow('isbn', $element);
                $this->setRow('volume', $element);
                $this->setRow('part', $element);
                $this->setRow('language', $element);
                $this->setRow('table_of_contents', $element);
                $this->setRow('type', $element);
                $this->setRow('performed_by', $element);
                $this->setRow('performance_type', $element);
                $this->setRow('storage', $element);
                $this->setRow('format', $element);
                $this->setRow('recording_length', $element);
                break;
            
            case OfferType::MEDIA:
                $this->setRow('artist', $element);
                $this->setRow('title', $element);
                $this->setRow('year', $element);
                $this->setRow('media', $element);
                $this->setRow('starring', $element);
                $this->setRow('director', $element);
                $this->setRow('originalName', $element);
                $this->setRow('country', $element);
                break;

            case OfferType::DRUGS:
                $this->setRow('name', $element);
                $this->setRow('vendor', $element);
                $this->setRow('vendorCode', $element);
                break;
            
            case OfferType::TICKET:
                $this->setRow('name', $element);
                $this->setRow('place', $element);
                $this->setRow('hall', $element);
                $this->setRow('hall_part', $element);
                $this->setRow('date', $element);
                $this->setRow('is_premiere', $element);
                $this->setRow('is_kids', $element);
                break;
            
            case OfferType::TOUR:
                $this->setRow('name', $element);
                $this->setRow('worldRegion', $element);
                $this->setRow('country', $element);
                $this->setRow('region', $element);
                $this->setRow('days', $element);
                $this->setRow('dataTour', $element);
                $this->setRow('hotel_stars', $element);
                $this->setRow('room', $element);
                $this->setRow('meal', $element);
                $this->setRow('included', $element);
                $this->setRow('transport', $element);
                $this->setRow('price_min', $element);
                $this->setRow('price_max', $element);
                $this->setRow('options', $element);
                break;
        }
        
        $this->setRow('url', $element);
        $this->setRow('price', $element);
        $this->setRow('oldprice', $element);
        $this->setRow('currencyId', $element);
        $this->setRow('category', $element);
        
        if (count($element->picture)) {
            $this->setData('picture', $element->picture[0]);
        }
        
        $this->setRow('description', $element);
        
        $this->setRow('delivery', $element);
        $this->setRow('local_delivery_days', $element);
        $this->setRow('local_delivery_cost', $element);
        $this->setRow('pickup', $element);
        $this->setRow('store', $element);
        $this->setRow('sales_notes', $element);
        $this->setRow('min-quantity', $element, 'min_quantity');
        $this->setRow('step-quantity', $element, 'step_quantity');
        $this->setRow('manufacturer_warranty', $element);
        $this->setRow('country_of_origin', $element);
        $this->setRow('adult', $element);
        $this->setRow('age', $element);
        $this->setRow('barcode', $element);
        $this->setRow('cpa', $element);
        $this->setRow('expiry', $element);
        $this->setRow('weight', $element);
        $this->setRow('dimensions', $element);
        $this->setRow('downloadable', $element);
        
        $this->rows[] = $this->row;
    }
    
    /**
     * Set current row value from element attribute.
     *
     * @param type $name
     * @param Common $element
     * @param string $attribute Element attribute name.
     */
    protected function setRow($name, Common $element, $attribute = '')
    {
        $prop = empty($attribute) ? $element->$name : $element->$attribute;
        $this->setData($name, empty($prop) ? '' : $prop);
    }
    
    /**
     * Set current row value.
     *
     * @param string $name
     * @param string $value
     */
    protected function setData($name, $value)
    {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        $this->row[$name] = Tools::htmlentitiesDecodeUTF8(Tools::nl2br($value));
    }
    
    /**
     * @return string[]
     * @throws Exception When no rows are added.
     */
    protected function getHeaderColumns()
    {
        if (!count($this->rows)) {
            throw new Exception('Cannot get header columns, no rows added.');
        }
        return array_keys($this->rows[0]);
    }
    
    /**
     * @inheritdoc
     */
    public function reset()
    {
        $this->row = [];
        $this->rows = [];
    }
}
