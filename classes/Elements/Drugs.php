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
 * Drugs offer type.
 *
 * @link https://yandex.ru/support/partnermarket/export/medicine.html
 * @author skoro
 */
class Drugs extends Common
{

    public $type;
    
    public $name;
    
    public $vendor;
    
    public $vendorCode;
    
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return OfferType::DRUGS;
    }

    /**
     * @inheritdoc
     */
    public function getValidators()
    {
        return array_merge(parent::getValidators(), [
            'type' => ['isRequired'],
            'name' => ['isRequired', ['Validate', 'isGenericName']],
            'vendor' => [['Validate', 'isGenericName']],
            'vendorCode' => [['Validate', 'isGenericName']],
            'pickup' => ['isRequired', ['Validate', 'isBool']],
            'delivery' => ['isRequired', ['Validate', 'isBool']],
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function initType()
    {
        $this->type = 'medicine';
        $this->pickup = true;
        $this->delivery = false;
        
        $this->name = $this->product->name[$this->id_lang];
        $manufacturer = new \Manufacturer($this->product->id_manufacturer);
        if (\Validate::isLoadedObject($manufacturer)) {
            $this->vendor = $manufacturer->name;
            $this->vendorCode = $this->product->id_manufacturer;
        }
    }
}
