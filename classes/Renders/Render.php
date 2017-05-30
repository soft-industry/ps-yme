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

use SI\YandexMarket\Elements\Common;

/**
 * Base render class.
 *
 * @author skoro
 */
abstract class Render
{
    
    /**
     * @var array Render options.
     */
    protected $options;
    
    /**
     * Creates a new render.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = array_merge($this->getDefaultOptions(), $options);
        $this->init();
    }
    
    /**
     * Initialize render.
     */
    protected function init()
    {
    }
    
    /**
     * Render default options.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return array();
    }
    
    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Render header (before processing products).
     *
     * @return string
     */
    public function getHeader()
    {
    }
    
    /**
     * Render footer (after processing products).
     *
     * @return string
     */
    public function getFooter()
    {
    }
    
    /**
     * Render offer element.
     *
     * @param Common $element
     */
    abstract public function renderElement(Common $element);
    
    /**
     * Flush offers content.
     *
     * @return string
     */
    abstract public function flush();
    
    /**
     * Reset render to its initial state.
     */
    public function reset()
    {
    }
}
