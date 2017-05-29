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
 * Arbitraty offer type.
 *
 * @link https://yandex.ru/support/partnermarket/export/vendor-model.html
 * @author skoro
 */
class Arbitraty extends Common
{
    
    public $type;
    
    public $model;
    
    public $vendor;
    
    public $vendorCode;
    
    public $typePrefix;
    
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return OfferType::ARBITRARY;
    }
    
    /**
     * @inheritdoc
     */
    public function getValidators()
    {
        return array_merge(parent::getValidators(), [
            'type' => ['isRequired'],
            'model' => ['isRequired', ['Validate', 'isGenericName']],
            'vendor' => ['isRequired', ['Validate', 'isGenericName']],
            'vendorCode' => [['Validate', 'isGenericName']],
            'typePrefix' => [['Validate', 'isGenericName']],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    protected function initType()
    {
        $this->type = 'vendor.model';
        $this->model = $this->product->name[$this->id_lang];
        
        $this->createTypePrefix();

        $manufacturer = new \Manufacturer($this->product->id_manufacturer);
        if (\Validate::isLoadedObject($manufacturer)) {
            $this->vendor = $manufacturer->name;
            $this->vendorCode = $this->product->id_manufacturer;
        }
    }
    
    protected function createTypePrefix()
    {
        $tokens = explode(' ', $this->category);
        if (count($tokens) === 1) {
            $category = new \Category($this->categoryId);
            if (\Validate::isLoadedObject($category)) {
                $parent = new \Category($category->id_parent);
                if (\Validate::isLoadedObject($parent)) {
                    $this->typePrefix = $parent->name[$this->id_lang];
                    return;
                }
            }
        }
        $this->typePrefix = $this->category;
    }
}
