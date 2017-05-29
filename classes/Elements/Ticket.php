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
 * Ticket offer type.
 *
 * @author skoro
 */
class Ticket extends Common
{

    public $type;
    
    public $name;
    
    public $place;
    
    public $hall;
    
    public $hall_part;
    
    public $date;
    
    public $is_premiere;
    
    public $is_kids;

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return OfferType::TICKET;
    }

    /**
     * @inheritdoc
     */
    public function getValidators()
    {
        return array_merge(parent::getValidators(), [
            'type' => ['isRequired'],
            'name' => ['isRequired', ['Validate', 'isGenericName']],
            'place' => ['isRequired', ['Validate', 'isGenericName']],
            'hall' => [['Validate', 'isGenericName']],
            'hall_part' => [['Validate', 'isGenericName']],
            'date' => ['isRequired', [$this, 'validateDate']],
            'is_premiere' => [['Validate', 'isBool']],
            'is_kids' => [['Validate', 'isBool']],
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function initType()
    {
        $this->type = 'event-ticket';
        $this->name = $this->product->name[$this->id_lang];
    }
    
    /**
     * Validate date.
     *
     * @param string $attribute
     * @return bool
     */
    public function validateDate($attribute)
    {
        $result = strtotime($this->$attribute);
        return $result !== false;
    }
}
