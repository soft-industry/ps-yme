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

/**
 * YML render.
 *
 * @link https://yandex.ru/support/partnermarket/yml/about-yml.html
 * @author skoro
 */
class YML extends Render
{
    
    /**
     * @var string[] Rendered elements.
     */
    protected $elements = array();
    
    /**
     * @var string[] Xml 'offer' tag list of attributes.
     */
    protected $offerAttributes = array();
    
    /**
     * @inheritdoc
     */
    public function getDefaultOptions()
    {
        return array(
            'charset'       => 'UTF-8', // windows-1251
            'catalog_date'  => time(),
            'company'       => '',
            'shop_name'     => '', // Required.
            'shop_url'      => '', // Required.
            'currencies'    => array(), // list or callable.
            'categories'    => array(), // list or callable.
        );
    }
    
    /**
     * @inheritdoc
     */
    public function getHeader()
    {
        return sprintf(
            "<?xml version=\"1.0\" encoding=\"%s\"?>\n<yml_catalog date=\"%s\">\n<shop>\n",
            $this->options['charset'],
            date('Y-m-d H:i', $this->options['catalog_date'])
        )
            . $this->getShopInfo()
            . $this->getCurrencies()
            . $this->getCategories();
    }
    
    /**
     * @inheritdoc
     */
    public function getFooter()
    {
        return "</shop>\n</yml_catalog>\n";
    }
    
    /**
     * Renders shop information tags: name, url and company.
     *
     * @return string
     */
    public function getShopInfo()
    {
        if (empty($this->options['shop_url'])) {
            throw new Exception('Option "shop_url" is required.');
        }
        
        if (empty($this->options['shop_name'])) {
            throw new Exception('Option "shop_name" is required.');
        }
        
        if (empty($this->options['company'])) {
            $this->options['company'] = $this->options['shop_name'];
        }

        return $this->getTags(array(
            'name'    => $this->options['shop_name'],
            'url'     => $this->options['shop_url'],
            'company' => $this->options['company'], // TODO: encode company.
        ));
    }
    
    /**
     * Renders currencies section.
     *
     * @return string
     */
    public function getCurrencies()
    {
        $currencies = $this->options['currencies'];
        if (is_callable($currencies)) {
            $currencies = call_user_func($currencies);
        } elseif (!is_array($currencies)) {
            throw new Exception('Option "currencies" must be a list or a callable.');
        }
        
        $tags = array_map(function (array $currency) {
            return $this->getTag('currency', null, $currency);
        }, $currencies);
        
        return $this->getTag('currencies', $tags);
    }
    
    /**
     * Renders categories section.
     *
     * @return string
     */
    public function getCategories()
    {
        $categories = $this->options['categories'];
        if (is_callable($categories)) {
            $categories = call_user_func($categories);
        } elseif (!is_array($categories)) {
            throw new Exception('Option "categories" must be a list or a callable.');
        }
        
        $tags = array_map(function (array $category) {
            return $this->getTag('category', $category['name'], array(
                'id'       => $category['id'],
                'parentId' => $category['parentId'] == 0 ? '' : $category['parentId'],
            ));
        }, $categories);
        
        return $this->getTag('categories', $tags);
    }

    /**
     * @inheritdoc
     */
    public function renderElement(Common $element)
    {
        $offer = array();
        
        $this->appendTag('url', $element, $offer);
        $this->appendTag('price', $element, $offer);
        $this->appendTag('oldprice', $element, $offer);
        $this->appendTag('currencyId', $element, $offer);
        $this->appendTag('categoryId', $element, $offer);

        foreach ($element->picture as $picture) {
            $offer[] = $this->getTag('picture', $picture);
        }
        
        $this->appendTag('delivery', $element, $offer);
        // TODO: delivery-options
        $this->appendTag('pickup', $element, $offer);
        $this->appendTag('available', $element, $offer);
        $this->appendTag('store', $element, $offer);
        // TODO: outlets
        if ($element->description) {
            $offer[] = $this->getTag('description', '<![CDATA[' . htmlentities($element->description) . ']]>');
        }
        $this->appendTag('sales_notes', $element, $offer);
        $this->appendTag('min-quantity', $element, $offer, 'min_quantity');
        $this->appendTag('step-quantity', $element, $offer, 'step_quantity');
        $this->appendTag('manufacturer_warranty', $element, $offer);
        $this->appendTag('country_of_origin', $element, $offer);
        $this->appendTag('adult', $element, $offer);
        
        if ($element->age) {
            $offer[] = $this->getTag('age', $element->age, array(
                'unit' => $element->age_unit,
            ));
        }
        
        foreach ($element->barcode as $barcode) {
            $offer[] = $this->getTag('barcode', $barcode);
        }
        
        $this->appendTag('cpa', $element, $offer);
        
        foreach ($element->param as $name => $value) {
            $offer[] = $this->getTag('param', $value, array(
                'name' => $name,
            ));
        }
        
        $this->appendTag('expiry', $element, $offer);
        $this->appendTag('weight', $element, $offer);
        $this->appendTag('dimensions', $element, $offer);
        $this->appendTag('downloadable', $element, $offer);
        $this->appendTag('group_id', $element, $offer);
        $this->appendTag('rec', $element, $offer);
        
        $this->offerAttributes = array('id', 'available', 'cbid', 'bid', 'fee');
        
        $this->renderByType($element, $offer);
        
        $this->elements[] = $this->renderOffer($element, $offer);
    }
    
    /**
     * @inheritdoc
     */
    public function flush()
    {
        return $this->getTag('offers', implode("\n", $this->elements));
    }
    
    /**
     * Render specific offer type elements.
     *
     * @param Common $element
     * @param array $offer
     */
    protected function renderByType(Common $element, array &$offer)
    {
        $props = array();

        switch ($element->getType()) {
            case OfferType::SIMPLE:
                $props = array('name', 'model', 'vendor', 'vendorCode');
                break;
            
            case OfferType::ARBITRARY:
                $props = array('model', 'vendor', 'vendorCode', 'typePrefix');
                $this->offerAttributes[] = 'type';
                break;
            
            case OfferType::BOOK:
                $props = array(
                    'name', 'author', 'publisher', 'series', 'year',
                    'isbn', 'volume', 'part', 'language', 'table_of_contents',
                    'binding', 'page_extent',
                );
                $this->offerAttributes[] = 'type';
                break;
            
            case OfferType::AUDIOBOOK:
                $props = array(
                    'name', 'author', 'publisher', 'series', 'year',
                    'isbn', 'volume', 'part', 'language', 'table_of_contents',
                    'performed_by', 'performance_type', 'storage', 'format',
                    'recording_length',
                );
                $this->offerAttributes[] = 'type';
                break;
            
            case OfferType::MEDIA:
                $props = array(
                    'artist', 'title', 'year', 'media', 'starring',
                    'director', 'originalName', 'country',
                );
                $this->offerAttributes[] = 'type';
                break;
            
            case OfferType::DRUGS:
                $props = array('name', 'vendor', 'vendorCode');
                $this->offerAttributes[] = 'type';
                break;
            
            case OfferType::TICKET:
                $props = array(
                    'name', 'place', 'hall', 'hall_part', 'date',
                    'is_premiere', 'is_kids',
                );
                $this->offerAttributes[] = 'type';
                break;
            
            case OfferType::TOUR:
                $props = array(
                    'name', 'worldRegion', 'country', 'region', 'days',
                    'dataTour', 'hotel_stars', 'room', 'meal', 'included',
                    'transport', 'price_min', 'price_max', 'options',
                );
                $this->offerAttributes[] = 'type';
                break;
        }
        
        foreach ($props as $prop) {
            $this->appendTag($prop, $element, $offer);
        }
    }
    
    /**
     * Renders 'offer' tag.
     *
     * @param Common $element
     * @param array $offer
     * @return string
     */
    protected function renderOffer(Common $element, array $offer)
    {
        $attributes = array();
        
        foreach ($this->offerAttributes as $name) {
            $attributes[$name] = $element->$name;
        }
        
        return $this->getTag('offer', $offer, $attributes);
    }
    
    /**
     * Append XML tag to buffer.
     *
     * @param string $tag
     * @param Common $element
     * @param array $buffer Rendered tags buffer.
     * @param string $attribute Element attribute.
     * @return string|null
     */
    protected function appendTag($tag, Common $element, array &$buffer, $attribute = '')
    {
        if (empty($attribute)) {
            $attribute = $tag;
        }

        if (isset($element->$attribute)) {
            $line = $this->getTag($tag, $element->$attribute);
            $buffer[] = $line;
            return $line;
        }
    }
    
    /**
     * Renders XML tag.
     *
     * @param string $tag
     * @param string|array $content
     * @param array $attributes
     * @return string
     */
    protected function getTag($tag, $content, array $attributes = array())
    {
        if (empty($content) && empty($attributes)) {
            return '';
        }
        
        $bool_cast = function ($v) {
            return is_bool($v) ? ($v ? 'true' : 'false') : $v;
        };

        $attrs = '';
        foreach ($attributes as $name => $value) {
            if (!empty($value)) {
                $value = $bool_cast($value);
                $attrs .= " $name=\"$value\"";
            }
        }
        
        $content = $bool_cast($content);
        if (is_array($content)) {
            $content = implode("\n", $content);
        }

        return "<{$tag}{$attrs}>$content</$tag>";
    }
    
    /**
     * Renders tags.
     *
     * @param array $tags
     * @return string
     */
    protected function getTags($tags)
    {
        $output = '';

        foreach ($tags as $tag => $content) {
            $output .= $this->getTag($tag, $content) . "\n";
        }

        return $output;
    }
}
