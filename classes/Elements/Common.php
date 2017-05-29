<?php
/**
 * 2017 Soft Industry
 *
 *   @author    Skorobogatko Alexei <a.skorobogatko@soft-industry.com>
 *   @copyright 2017 Soft-Industry
 *   @license   http://opensource.org/licenses/afl-3.0.php
 *   @since     0.1.0
 */

namespace SI\YandexMarket\Elements;

use Product;
use Context;
use Validate;

/**
 * Common offer element.
 *
 * @author skoro
 */
abstract class Common
{
    
    public $id;
    
    public $cbid;
    
    public $bid;
    
    public $fee;
    
    public $url;
    
    public $price;
    
    public $oldprice;
    
    public $currencyId;
    
    public $categoryId;
    
    public $category;
    
    public $picture = [];
    
    public $delivery;
    
    public $local_delivery_days;
    
    public $local_delivery_cost;
    
    public $pickup;
    
    public $available;
    
    public $store;
    
    public $outlets;
    
    public $description;
    
    public $sales_notes;
    
    public $min_quantity;
    
    public $step_quantity;
    
    public $manufacturer_warranty;
    
    public $country_of_origin;
    
    public $adult;
    
    public $age;
    
    public $age_unit;
    
    public $barcode = [];
    
    public $cpa;
    
    public $param = [];
    
    public $expiry;
    
    public $weight;
    
    public $dimensions;
    
    public $downloadable;
    
    public $group_id;
    
    public $rec;
    
    /**
     * @var Product
     */
    protected $product;
    
    /**
     * @var boolean
     */
    protected $isValid;
    
    /**
     * @var array Validation errors.
     */
    protected $errors = [];
    
    /**
     * @var integer
     */
    protected $id_lang;
    
    /**
     * @var array Mapping between element property and product feature 
     * in format 'key => value', where key is element property and 
     * value is product feature name.
     */
    protected $featuresMap = [];
    
    /**
     * Creates a new element.
     *
     * @param Product $product
     * @param array $featuresMap Optional. Element properties and product
     * features mapping.
     */
    public function __construct(Product $product, array $featuresMap = [])
    {
        $this->product = $product;
        $this->id_lang = Context::getContext()->language->id;
        $this->featuresMap = $featuresMap;
        $this->init();
        $this->validate();
    }
    
    /**
     * Get element validators.
     *
     * @return array Returns list where index is element property and value
     * is list of validators (callable type).
     */
    public function getValidators()
    {
        return [
            'id' => ['isRequired', ['Validate', 'isUnsignedId']],
            'url' => ['isRequired', ['Validate', 'isAbsoluteUrl']],
            'price' => ['isRequired', ['Validate', 'isPrice']],
            'oldprice' => [['Validate', 'isPrice']],
            'currencyId' => ['isRequired'],
            'categoryId' => ['isRequired', ['Validate', 'isUnsignedId']],
            'category' => ['isRequired', ['Validate', 'isCatalogName']],
            'picture' => [['Validate', 'isAbsoluteUrl']],
            'delivery' => [['Validate', 'isBool']],
        ];
    }
    
    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }
    
    /**
     * Is element validated ?
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->isValid;
    }
    
    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Initialize element.
     */
    protected function init()
    {
        $category = $this->getDefaultCategory();

        $this->id = $this->product->id;
        $this->url = Context::getContext()->link->getProductLink($this->product);
        $this->price = number_format((float) $this->product->price, 2);
        $this->currencyId = $this->getCurrencyIso();
        $this->categoryId = $category->id;
        $this->category = $category->name[$this->id_lang];
        $this->picture = $this->getPictures();
        
        $this->available = (bool) ($this->product->active == 1 && $this->product->available_for_order);
        $this->store = !$this->product->online_only;
        $this->description = trim($this->product->description[$this->id_lang]);
        $this->weight = ((float) $this->product->weight > 0) ? number_format($this->product->weight, 3) : null;
        $this->dimensions = $this->getDimensions();
        
        $this->param = $this->getParams();

        $this->setPropsByFeatures();
        $this->initType();
    }
    
    /**
     * Initialize rest attributes by descent types.
     */
    protected function initType()
    {
    }
    
    protected function getCurrencyIso()
    {
        $currency = \Currency::getDefaultCurrency();
        if ($currency === false) {
            throw new Exception('Cannot get default currency.');
        }
        return $currency->iso_code;
    }
    
    /**
     * @throws Exception When category cannot be loaded.
     * @return \Category
     */
    protected function getDefaultCategory()
    {
        $category = new \Category($this->product->id_category_default);
        if (!Validate::isLoadedObject($category)) {
            throw new Exception('Cannot load category: ' . $this->product->id_category_default);
        }
        return $category;
    }
    
    /**
     * Returns list of image url.
     */
    protected function getPictures()
    {
        $images = $this->product->getImages($this->id_lang);
        if (!$images) {
            return;
        }

        $link = Context::getContext()->link;
        $pictures = [];

        foreach ($images as $image) {
            $url = $link->getImageLink($this->product->link_rewrite[$this->id_lang], $image['id_image']);
            if ($image['cover'] == 1) {
                array_unshift($pictures, $url);
            } else {
                $pictures[] = $url;
            }
        }

        return $pictures;
    }
    
    /**
     * Validate element attributes.
     *
     * @return array
     * @throws \RuntimeException When validator is not callable.
     */
    public function validate()
    {
        $this->errors = [];
        $this->isValid = false;

        // Last chance to change element properties.
        \Hook::exec('offerElementBeforeValidate', [
            'element' => $this,
        ]);

        foreach ($this->getValidators() as $attribute => $validators) {
            foreach ($validators as $validator) {
                if ($validator === 'isRequired') {
                    $result = !empty($this->$attribute);
                }
                elseif ($this->$attribute) {
                    $value = $this->$attribute;
                    if (!is_callable($validator)) {
                        throw new \RuntimeException('Validator must be instance method or callable.');
                    }
                    if (is_array($value)) {
                        foreach ($value as $item) {
                            $result = call_user_func($validator, $item);
                            if (!$result) {
                                break;
                            }
                        }
                    } else {
                        $result = call_user_func($validator, $value);
                    }
                }
                if (!$result) {
                    $this->errors[$attribute][] = $validator;
                }
            }
        }

        return $this->isValid = empty($this->errors);
    }
    
    /**
     * Returns element type (offer type).
     *
     * @return string
     */
    abstract public function getType();
    
    /**
     * Get dimensions formatted string.
     *
     * @return string|null
     */
    protected function getDimensions()
    {
        $height = (float) $this->product->height;
        $width = (float) $this->product->width;
        $depth = (float) $this->product->depth;
        
        if ($height && $width && $depth) {
            return sprintf('%.3f/%.3f/%.3f', $height, $width, $depth);
        }
    }
    
    /**
     * Get product parameters.
     *
     * It's based on product features.
     *
     * @return array
     */
    protected function getParams()
    {
        if (!($features = $this->product->getFeatures())) {
            return [];
        }
        
        $params = [];
        
        foreach ($features as $data) {
            $feature = \Feature::getFeature($this->id_lang, $data['id_feature']);
            $value = new \FeatureValue($data['id_feature_value']);
            if (Validate::isLoadedObject($value)) {
                $params[$feature['name']] = $value->value[$this->id_lang];
            }
        }
        
        return $params;
    }
    
    /**
     * Initialize element properties from the features map.
     */
    protected function setPropsByFeatures()
    {
        $map = array_flip($this->featuresMap);

        foreach ($this->param as $name => $value) {
            if (isset($map[$name]) && property_exists($this, $map[$name])) {
                $prop = $map[$name];
                $this->{$prop} = $value;
                unset($this->param[$name]);
            }
        }
    }
    
    /**
     * Return true if attribute exists and not empty.
     *
     * @param string $attribute
     * @return boolean
     */
    public function isRequired($attribute)
    {
        return !empty($this->$attribute);
    }
}
