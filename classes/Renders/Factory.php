<?php
/**
 * 2017 Soft Industry
 *
 *   @author    Skorobogatko Alexei <a.skorobogatko@soft-industry.com>
 *   @copyright 2017 Soft-Industry
 *   @license   http://opensource.org/licenses/afl-3.0.php
 *   @since     0.1.0
 */

namespace SI\YandexMarket\Renders;

/**
 * Render factory.
 *
 * @author skoro
 */
class Factory
{
    /**
     * Render types.
     */
    const YML = 'YML';
    const CSV = 'CSV';
    const XLS = 'XLS';
    
    /**
     * 
     * @param string $type Render type.
     * @param array $options Optional. Options which render accept.
     * @return Render
     * @throws Exception
     */
    public static function create($type, array $options = [])
    {
        switch ($type) {
            case self::YML:
                return new YML($options);
            
            case self::CSV:
                return new CSV($options);
                
            case self::XLS:
                return new Excel($options);
            
            default:
                throw new Exception(sprintf('Render "%s" does not implemented.', $type));
        }
    }
    
    /**
     * Get list of render types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::YML => 'Yandex Market YML',
            self::CSV => 'CSV',
            self::XLS => 'Excel document',
        ];
    }
}
