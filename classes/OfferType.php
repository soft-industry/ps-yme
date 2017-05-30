<?php
/**
 * 2017 Soft Industry
 *
 *   @author    Skorobogatko Alexei <a.skorobogatko@soft-industry.com>
 *   @copyright 2017 Soft-Industry
 *   @license   http://opensource.org/licenses/afl-3.0.php
 *   @since     0.1.0
 */

namespace SI\YandexMarket;

/**
 * OfferType
 *
 * @author skoro
 */
class OfferType
{
    const SIMPLE    = 'simple';
    const ARBITRARY = 'arbitrary';
    const BOOK      = 'book';
    const AUDIOBOOK = 'audiobook';
    const MEDIA     = 'media';
    const DRUGS     = 'drugs';
    const TICKET    = 'ticket';
    const TOUR      = 'tour';

    /**
     * @var string
     */
    protected $type;
    
    /**
     * @var array
     */
    protected $features = array();
    
    /**
     * Creates a new offer type.
     *
     * @param string $type Offer type name.
     * @param array $features Map element properties to product features.
     */
    public function __construct($type = self::SIMPLE, array $features = array())
    {
        $this->type = $type;
        $this->features = $features;
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Creates an offer element from a product model.
     *
     * @param \Product $product
     * @return Elements\Common
     * @throws Exception
     */
    public function createElement(\Product $product)
    {
        switch ($this->type) {
            case self::SIMPLE:
                return new Elements\Simple($product, $this->features);
                
            case self::ARBITRARY:
                return new Elements\Arbitraty($product, $this->features);

            case self::BOOK:
                return new Elements\Book($product, $this->features);
                
            case self::AUDIOBOOK:
                return new Elements\Audiobook($product, $this->features);
            
            case self::MEDIA:
                return new Elements\Media($product, $this->features);
            
            case self::DRUGS:
                return new Elements\Drugs($product, $this->features);
            
            case self::TICKET:
                return new Elements\Ticket($product, $this->features);
            
            case self::TOUR:
                return new Elements\Tour($product, $this->features);
        }
        
        throw new Exception('Type does not implemented yet.');
    }
    
    /**
     * Returns a list of required product features for this offer type.
     */
    public function getFeatures()
    {
        $common = array(
            'country_of_origin' => 'Country of origin',
            'sales_notes' => 'Sales notes',
            'age' => 'Age',
        );
        $features = array();

        switch ($this->type) {
            case self::BOOK:
                $features = array(
                    'author' => 'Author',
                    'publisher' => 'Publisher',
                    'series' => 'Series',
                    'year' => 'Year',
                    'isbn' => 'ISBN',
                    'volume' => 'Volume',
                    'part' => 'Part',
                    'language' => 'Language',
                    'table_of_contents' => 'Table of contents',
                    'binding' => 'Binding',
                    'page_extent' => 'Page extent',
                );
                break;
            
            case self::AUDIOBOOK:
                $features = array(
                    'author' => 'Author',
                    'publisher' => 'Publisher',
                    'series' => 'Series',
                    'year' => 'Year',
                    'isbn' => 'ISBN',
                    'volume' => 'Volume',
                    'part' => 'Part',
                    'language' => 'Language',
                    'table_of_contents' => 'Table of contents',
                    'performed_by' => 'Performed by',
                    'performance_type' => 'Performance type',
                    'storage' => 'Storage',
                    'format' => 'Format',
                    'recording_length' => 'Recording length',
                );
                break;
            
            case self::MEDIA:
                $features = array(
                    'artist' => 'Artist',
                    'title' => 'Title',
                    'year' => 'Year',
                    'media' => 'Media',
                    'starring' => 'Starring',
                    'director' => 'Director',
                    'originalName' => 'Original name',
                    'country' => 'Country',
                );
                break;
            
            case self::TICKET:
                $features = array(
                    'place' => 'Place',
                    'hall' => 'Hall',
                    'hall_part' => 'Hall part',
                    'date' => 'Date',
                    'is_premiere' => 'Is premiere',
                    'is_kids' => 'Is kids',
                );
                break;
            
            case self::TOUR:
                $features = array(
                    'worldRegion' => 'World region',
                    'country' => 'Country',
                    'region' => 'Region',
                    'days' => 'Days',
                    'dataTour' => 'Tour date',
                    'hotel_stars' => 'Hotel stars',
                    'room' => 'Room (SNG, DBL, etc.)',
                    'meal' => 'Meal (All, HB, etc.)',
                    'included' => 'What is included in tour',
                    'transport' => 'Transport',
                    'price_min' => 'Price min',
                    'price_max' => 'Price max',
                    'options' => 'Options',
                );
                break;
            
        }
        
        return array_merge($common, $features);
    }
    
    /**
     * Get list of available offer types and their titles.
     *
     * @return array
     */
    public static function getTypes()
    {
        return array(
            self::SIMPLE => 'Simple',
            self::ARBITRARY => 'Arbitrary',
            self::BOOK => 'Book',
            self::AUDIOBOOK => 'Audio book',
            self::MEDIA => 'Media content',
            self::DRUGS => 'Drugs',
            self::TICKET => 'Ticket',
            self::TOUR => 'Tour',
        );
    }
}
