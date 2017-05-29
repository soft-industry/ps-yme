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

use SI\YandexMarket\OfferType;

/**
 * Simple offer type.
 *
 * @link https://yandex.ru/support/partnermarket/offers.html
 * @author skoro
 */
class Simple extends Common
{
    
    /**
     * @var string
     */
    public $name;
    
    /**
     * @var string
     */
    public $model;
    
    /**
     * @var string
     */
    public $vendor;

    /**
     * @var string
     */
    public $vendorCode;
    
    /**
     * @inheritdoc
     */
    public function getValidators()
    {
        return array_merge(parent::getValidators(), [
            'name' => ['isRequired', ['Validate', 'isGenericName']],
            'model' => [['Validate', 'isGenericName']],
            'vendor' => [['Validate', 'isGenericName']],
            'vendorCode' => [['Validate', 'isGenericName']],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return OfferType::SIMPLE;
    }
    
    /**
     * @inheritdoc
     */
    protected function initType()
    {
        $this->name = $this->product->name[$this->id_lang];
        $this->model = $this->product->reference;
        
        $manufacturer = new \Manufacturer($this->product->id_manufacturer);
        if (\Validate::isLoadedObject($manufacturer)) {
            $this->vendor = $manufacturer->name;
            $this->vendorCode = $this->product->id_manufacturer;
        }
    }
}
