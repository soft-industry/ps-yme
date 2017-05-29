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
 * Media offer type.
 *
 * @link https://yandex.ru/support/partnermarket/export/music-video.html
 * @author skoro
 */
class Media extends Common
{
    
    public $type;
    
    public $artist;
    
    public $title;
    
    public $year;
    
    public $media;
    
    public $starring;
    
    public $director;
    
    public $originalName;
    
    public $country;

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return OfferType::MEDIA;
    }

    /**
     * @inheritdoc
     */
    public function getValidators()
    {
        return array_merge(parent::getValidators(), [
            'type' => ['isRequired'],
            'artist' => [['Validate', 'isGenericName']],
            'title' => [['Validate', 'isGenericName']],
            'year' => [['Validate', 'isUnsignedInt']],
            'media' => [['Validate', 'isGenericName']],
            'starring' => [['Validate', 'isGenericName']],
            'director' => [['Validate', 'isGenericName']],
            'originalName' => [['Validate', 'isGenericName']],
            'country' => [['Validate', 'isGenericName']],
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function initType()
    {
        $this->type = 'artist.title';
        $this->title = $this->product->name[$this->id_lang];
    }
}
