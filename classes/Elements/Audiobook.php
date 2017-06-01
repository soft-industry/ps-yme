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
        return array_merge(parent::getValidators(), array(
            'name' => array(
                'isRequired',
                array('Validate', 'isGenericName')
            ),
            'author' => array(
                array('Validate', 'isGenericName'),
            ),
            'publisher' => array(
                array('Validate', 'isGenericName'),
            ),
            'series' => array(
                array('Validate', 'isGenericName'),
            ),
            'year' => array(
                array('Validate', 'isUnsignedInt'),
            ),
            'isbn' => array('isRequired'),
            'volume' => array(
                array('Validate', 'isUnsignedInt'),
            ),
            'part' => array(
                array('Validate', 'isUnsignedInt'),
            ),
            'language' => array(
                array('Validate', 'isGenericName'),
            ),
            'table_of_contents' => array(
                array('Validate', 'isGenericName'),
            ),
            'type' => array('isRequired'),
            'performed_by' => array(
                array('Validate', 'isGenericName'),
            ),
            'performance_type' => array(
                array('Validate', 'isGenericName'),
            ),
            'storage' => array(
                array('Validate', 'isGenericName'),
            ),
            'format' => array(
                array('Validate', 'isGenericName'),
            ),
            'recording_length' => array(
                array($this, 'validateRecordingLength'),
            ),
        ));
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
