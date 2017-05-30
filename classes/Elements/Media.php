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
        return array_merge(parent::getValidators(), array(
            'type' => array('isRequired'),
            'artist' => array(
                array('Validate', 'isGenericName'),
            ),
            'title' => array(
                array('Validate', 'isGenericName'),
            ),
            'year' => array(
                array('Validate', 'isUnsignedInt'),
            ),
            'media' => array(
                array('Validate', 'isGenericName'),
            ),
            'starring' => array(
                array('Validate', 'isGenericName'),
            ),
            'director' => array(
                array('Validate', 'isGenericName'),
            ),
            'originalName' => array(
                array('Validate', 'isGenericName'),
            ),
            'country' => array(
                array('Validate', 'isGenericName'),
            ),
        ));
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
