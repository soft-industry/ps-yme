<?php
/**
* 2017 Soft Industry
*
*   @author    Skorobogatko Alexei <a.skorobogatko@soft-industry.com>
*   @copyright 2017 Soft-Industry
*   @license   http://opensource.org/licenses/afl-3.0.php
*/

use SI\YandexMarket\OfferType;
use SI\YandexMarket\Renders\Factory as RenderFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/autoload.php';
require_once dirname(__FILE__) . '/vendor/autoload.php';

/**
 * Yandex Market
 *
 * Export products to Yandex Market.
 */
class Yme extends Module
{
    
    /**
     * Module contructor.
     */
    public function __construct()
    {
        $this->name = 'yme';
        $this->version = '0.1.0';
        $this->author = 'Soft Industry';
        $this->tab = 'export';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->need_instance = 0;
        $this->bootstrap = true;
        
        parent::__construct();
        
        $this->displayName = $this->l('Yandex Market');
        $this->description = $this->l('Export products to Yandex Market');
        
        $this->confirmUninstall = $this->l('Are you sure to uninstall module ?');
    }
    
    /**
     * Module installation.
     */
    public function install()
    {
        // Multishop.
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        
        return parent::install() &&
            $this->registerHook('offerElementBeforeValidate');
    }
    
    /**
     * Module uninstallation.
     */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        
        Configuration::deleteByName('YME_OFFER');
        Configuration::deleteByName('YME_EXPORT');
        Configuration::deleteByName('YME_EXPORT_FORMAT');
        
        return true;
    }
    
    /**
     * Process module configuration form.
     *
     * @return string
     */
    public function getContent()
    {
        $status = ''; // Message of last operation.
        $offer_types = OfferType::getTypes();
        $offer_features = $this->getOfferFeatures();

        // Update offer types.
        foreach ($offer_types as $offer_type => $offer_title) {
            if (!Tools::isSubmit("SubmitOfferFeatures_{$offer_type}")) {
                continue;
            }
            $offer = new OfferType($offer_type);
            foreach ($offer->getFeatures() as $feature => $feature_title) {
                $value_key = "YME_OFFER_{$offer_type}_{$feature}";
                $offer_features[$offer_type][$feature] = (int) Tools::getValue($value_key);
            }
            if (Configuration::updateValue('YME_OFFER', serialize($offer_features))) {
                $status .= $this->displayConfirmation(
                    $this->l($offer_title) . ' ' . $this->l('has been updated.')
                );
            }
        }
        
        // Update common module settings.
        if (Tools::isSubmit('SubmitSettings')) {
            $result = false; // Status result.

            $offer_type = Tools::getValue('YME_EXPORT');
            if (isset($offer_types[$offer_type])) {
                $result = Configuration::updateValue('YME_EXPORT', $offer_type);
            }
            
            $export_format = Tools::getValue('YME_EXPORT_FORMAT');
            $render_types = RenderFactory::getTypes();
            if (isset($render_types[$export_format])) {
                $result = Configuration::updateValue('YME_EXPORT_FORMAT', $export_format);
            }
            
            $enabled = (bool) Tools::getValue('YME_ENABLE');
            Configuration::updateValue('YME_ENABLE', $enabled);
            
            if ($result) {
                $status .= $this->displayConfirmation($this->l('Settings has been saved.'));
            }
        }
        
        return $status . $this->displayForm();
    }
    
    /**
     * Module configuration form.
     *
     * @return string
     */
    public function displayForm()
    {
        $helper = new HelperForm();
        $features = array_merge(array('-'), Feature::getFeatures($this->context->language->id));
        $offer_types = OfferType::getTypes();
        $render_types = RenderFactory::getTypes();

        $form = array();
        
        // Main settings form.
        $form[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Yandex Market Export settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable export'),
                        'name' => 'YME_ENABLE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'YME_ENABLE_YES',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'YME_ENABLE_NO',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Offer type'),
                        'name' => 'YME_EXPORT',
                        'default_value' => OfferType::SIMPLE,
                        'options' => array(
                            'query' => array_map(null, array_keys($offer_types), array_values($offer_types)),
                            'id' => 0,
                            'name' => 1,
                        ),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Output format'),
                        'name' => 'YME_EXPORT_FORMAT',
                        'default_value' => RenderFactory::YML,
                        'values' => array_map(function ($type, $title) {
                            return array(
                                'id' => $type,
                                'value' => $type,
                                'label' => $title,
                            );
                        }, array_keys($render_types), array_values($render_types)),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'SubmitSettings',
                ),
            ),
        );
        $helper->fields_value['YME_EXPORT'] = $this->getOfferType();
        $helper->fields_value['YME_EXPORT_FORMAT'] = $this->getExportFormat();
        $helper->fields_value['YME_ENABLE'] = $this->isExportEnabled();
        
        
        // Offer element forms.
        foreach ($offer_types as $type => $title) {
            $offer = new OfferType($type);
            $offer_features = $offer->getFeatures();
            if (empty($offer_features)) {
                continue;
            }
            
            $form_type = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l(Tools::ucfirst($type)),
                    ),
                    'input' => array(),
                ),
            );
            $inputs = &$form_type['form']['input'];
            
            foreach ($offer_features as $name => $title) {
                $input_name = "YME_OFFER_{$type}_{$name}";
                $inputs[] = array(
                    'type' => 'select',
                    'label' => $this->l($title),
                    'name' => $input_name,
                    'default_value' => '',
                    'options' => array(
                        'query' => $features,
                        'id' => 'id_feature',
                        'name' => 'name',
                    ),
                );
                $helper->fields_value[$input_name] = $this->getOfferFeatures($type, $name);
            }
            
            $form_type['form']['submit'] = array(
                'title' => $this->l('Save'),
                'name' => 'SubmitOfferFeatures_' . $type,
            );
            
            $form[] = $form_type;
        }
        
        return $helper->generateForm($form);
    }
    
    /**
     * Returns list of features assigned to types.
     *
     * @param string $type Optional. Offer type name.
     * @param string $prop Optional. Offer element property.
     * @param boolean $translated Optional. Translate feature id to feature name.
     * @return array|string
     */
    public function getOfferFeatures($type = null, $prop = null, $translated = false)
    {
        $features = unserialize(Configuration::get('YME_OFFER'));
        if ($features === false) {
            return array();
        }
        
        /**
         * Translate feature id to feature name.
         *
         * @param array|string $ids
         */
        $translate = function ($ids) use ($translated) {
            if (!$translated) {
                return $ids;
            }
            $id_lang = $this->context->language->id;
            if (is_array($ids)) {
                return array_map(function ($id) use ($id_lang) {
                    $feature = new Feature($id);
                    return Validate::isLoadedObject($feature) ? $feature->name[$id_lang] : null;
                }, $ids);
            }
            $feature = new Feature($ids);
            return Validate::isLoadedObject($feature) ? $feature->name[$id_lang] : null;
        };

        if ($type === null) {
            return $translate($features);
        }

        if (!isset($features[$type])) {
            return array();
        }

        if ($prop !== null) {
            return isset($features[$type][$prop]) ? $translate($features[$type][$prop]) : '';
        }
        
        return $translate($features[$type]);
    }
    
    /**
     * Get configured offer type.
     *
     * @return string
     */
    public function getOfferType()
    {
        $value = Configuration::get('YME_EXPORT');
        return $value === false ? OfferType::SIMPLE : $value;
    }
    
    /**
     * Get configured export format.
     *
     * @return string
     */
    public function getExportFormat()
    {
        $value = Configuration::get('YME_EXPORT_FORMAT');
        return $value === false ? RenderFactory::YML : $value;
    }
    
    /**
     * Is export enabled ?
     *
     * @return bool
     */
    public function isExportEnabled()
    {
        return (bool) Configuration::get('YME_ENABLE');
    }
}
