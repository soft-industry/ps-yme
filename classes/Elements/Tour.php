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
 * Tour offer type.
 *
 * @link https://yandex.ru/support/partnermarket/export/tours.html
 * @author skoro
 */
class Tour extends Common
{
    
    public $type;
    
    public $name;
    
    public $worldRegion;
    
    public $country;
    
    public $region;
    
    public $days;
    
    public $dataTour;
    
    public $hotel_stars;
    
    public $room;
    
    public $meal;
    
    public $included;
    
    public $transport;
    
    public $price_min;
    
    public $price_max;
    
    public $options;
    
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return OfferType::TOUR;
    }
    
    /**
     * @inheritdoc
     */
    public function getValidators()
    {
        return array_merge(parent::getValidators(), [
            'type' => ['isRequired'],
            'name' => ['isRequired', ['Validate', 'isGenericName']],
            'worldRegion' => [['Validate', 'isGenericName']],
            'country' => [['Validate', 'isGenericName']],
            'region' => [['Validate', 'isGenericName']],
            'days' => ['isRequired', ['Validate', 'isGenericName']], // FIXME: better to valid against INT ?
            'dataTour' => [['Validate', 'isGenericName']],
            'hotel_stars' => [['Validate', 'isGenericName']],
            'room' => [['Validate', 'isGenericName']],
            'meal' => [['Validate', 'isGenericName']],
            'included' => ['isRequired', ['Validate', 'isGenericName']],
            'transport' => ['isRequired', ['Validate', 'isGenericName']],
            'price_min' => [['Validate', 'isPrice']],
            'price_max' => [['Validate', 'isPrice']],
            'options' => [['Validate', 'isGenericName']],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    protected function initType()
    {
        $this->type = 'tour';
        $this->name = $this->product->name[$this->id_lang];
    }
}
