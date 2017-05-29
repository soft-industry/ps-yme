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
            'binding' => [['Validate', 'isGenericName']],
            'page_extent' => [['Validate', 'isUnsignedInt']],
        ]);
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
