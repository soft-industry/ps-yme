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
 * Book element type.
 *
 * @link https://yandex.ru/support/partnermarket/export/books.html
 * @author skoro
 */
class Book extends Common
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
    
    public $binding;
    
    public $page_extent;

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return OfferType::BOOK;
    }
    
    /**
     * @inheritdoc
     */
    public function getValidators()
    {
        return array_merge(parent::getValidators(), array(
            'name' => array('isRequired', array('Validate', 'isGenericName')),
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
            'binding' => array(
                array('Validate', 'isGenericName'),
            ),
            'page_extent' => array(
                array('Validate', 'isUnsignedInt'),
            ),
        ));
    }
    
    /**
     * @inheritdoc
     */
    protected function initType()
    {
        $this->type = 'book';
        $this->name = $this->product->name[$this->id_lang];
    }
}
