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
 * Audiobook offer type.
 *
 * @link https://yandex.ru/support/partnermarket/export/audiobooks.html
 * @author skoro
 */
class Audiobook extends Common
{
    
    public $name;
    
    public $author;
    
    public $publisher;
    
    public $series;
    
    public $year;
    
    public $isbn;
    
    public $volume;
    
    public $part;
    
    public $language;
    
    public $table_of_contents;
    
    public $type;
    
    public $performed_by;
    
    public $performance_type;

    public $storage;
    
    public $format;
    
    public $recording_length;
    
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return OfferType::AUDIOBOOK;
    }

    /**
     * @inheritdoc
     */
    public function getValidators()
    {
        return array_merge(parent::getValidators(), [
            'name' => ['isRequired', ['Validate', 'isGenericName']],
            'author' => [['Validate', 'isGenericName']],
            'publisher' => [['Validate', 'isGenericName']],
            'series' => [['Validate', 'isGenericName']],
            'year' => [['Validate', 'isUnsignedInt']],
            'isbn' => ['isRequired'],
            'volume' => [['Validate', 'isUnsignedInt']],
            'part' => [['Validate', 'isUnsignedInt']],
            'language' => [['Validate', 'isGenericName']],
            'table_of_contents' => [['Validate', 'isGenericName']],
            'type' => ['isRequired'],
            'performed_by' => [['Validate', 'isGenericName']],
            'performance_type' => [['Validate', 'isGenericName']],
            'storage' => [['Validate', 'isGenericName']],
            'format' => [['Validate', 'isGenericName']],
            'recording_length' => [[$this, 'validateRecordingLength']],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    protected function initType()
    {
        $this->type = 'audiobook';
        $this->name = $this->product->name[$this->id_lang];
    }
    
    /**
     * Validate 'recording_length' value.
     *
     * @param string $attribute
     * @return boolean
     */
    public function validateRecordingLength($attribute)
    {
        $value = $this->$attribute;
        return preg_match('/[0-9]{2}\.[0-9]{2}/', $value);
    }
}
