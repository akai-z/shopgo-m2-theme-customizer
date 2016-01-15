<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Block\Adminhtml\System\Design\Theme\Edit\Tab;

use Magento\Framework\App\Area;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Config\Model\Config\Source\Enabledisable;

/**
 * Theme form, customizer tab
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Customizer extends \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\AbstractTab
{
    /**
     * Whether theme is editable
     *
     * @var bool
     */
    protected $_isThemeEditable = true;

    /**
     * @var \Magento\Framework\File\Size
     */
    protected $_fileSize;

    /**
     * @var \ShopGo\ThemeCustomizer\Helper\Data
     */
    protected $_themeCustomizerHelper;

    /**
     * @var \ShopGo\ThemeCustomizer\Model\Source\ColorPickerPalette
     */
    protected $_colorPickerPalette;

    /**
     * @var \ShopGo\ThemeCustomizer\Model\Less
     */
    protected $_themeCustomizerLess;

    /**
     * @var array
     */
    protected $_fields;

    /**
     * @var array
     */
    protected $_fieldsSource;

    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * @var Enabledisable
     */
    protected $_enableDisable;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\File\Size $fileSize
     * @param \ShopGo\ThemeCustomizer\Helper\Data $themeCustomizerHelper
     * @param \ShopGo\ThemeCustomizer\Model\Source\ColorPickerPalette $colorPickerPalette
     * @param \ShopGo\ThemeCustomizer\Model\Less $themeCustomizerLess
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNo
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enableDisable
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\File\Size $fileSize,
        \ShopGo\ThemeCustomizer\Helper\Data $themeCustomizerHelper,
        \ShopGo\ThemeCustomizer\Model\Source\ColorPickerPalette $colorPickerPalette,
        \ShopGo\ThemeCustomizer\Model\Less $themeCustomizerLess,
        Yesno $yesNo,
        Enabledisable $enableDisable,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $objectManager, $data);

        $this->_fileSize = $fileSize;
        $this->_colorPickerPalette = $colorPickerPalette->getPalette();

        $this->_themeCustomizerHelper = $themeCustomizerHelper;
        $this->_themeCustomizerLess   = $themeCustomizerLess;

        $this->_themeCustomizerLess->setTheme(
            $this->_coreRegistry->registry('current_theme')->getCode()
        );

        $this->_fields = $this->_themeCustomizerLess->parseFieldsLess();
        $this->_fieldsSource = $this->_themeCustomizerLess->parseFieldsSourceLess();

        $this->_yesNo = $yesNo;
        $this->_enableDisable = $enableDisable;
    }

    /**
     * Create a form element with necessary controls
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $this->_addFieldsetsFromLess($form);
        $form->setFieldNameSuffix('theme');
        $this->setForm($form);

        return $this;
    }

    /**
     * Add special fields types attributes
     *
     * @param string $fieldType
     * @param string $id
     * @param array $fieldAttributes
     * @param string $value
     * @return array
     */
    protected function _addSpecialFieldTypesAttributes($fieldType, $id, $fieldAttributes, $value = '')
    {
        switch ($fieldType) {
            case 'color':
                // The line below is temporarily disabled, until a solution is found for spectrum plugin loading
                #$fieldAttributes['after_element_html'] = "<script>setColorPicker('{$id}', '{$value}')</script>";
                $fieldAttributes['after_element_html'] = $this->_addColorPickerScript($id, $value);
                break;
            case 'yesno':
                $yesno = $this->_yesNo->toOptionArray();
                $fieldAttributes['values'] = $yesno;
                break;
            case 'enabledisable':
                $enabledisable = $this->_enableDisable->toOptionArray();
                $fieldAttributes['values'] = $enabledisable;
                break;
        }

        return $fieldAttributes;
    }

    /**
     * Add field source
     *
     * @param string $fieldset
     * @param array $field
     * @return array
     */
    protected function _getFieldSource($fieldset, $field)
    {
        $source = [];

        if (isset($this->_fieldsSource[$fieldset])) {
            foreach ($this->_fieldsSource[$fieldset] as $_field) {
                if ($_field['type'] == $field['type']
                    && $_field['label'] == $field['label']) {
                    $source = $_field['value'];
                    break;
                }
            }
        }

        return $source;
    }

    /**
     * Add fields from LESS
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param array $fields
     * @return void
     */
    protected function _addFieldsFromLess($fieldset, $fields)
    {
        $fieldsetName = $fieldset->getId();

        foreach ($fields as $field) {
            $id    = "themecustomizer-{$fieldsetName}-{$field['type']}-{$field['label']}";
            $label = ucwords(str_replace('_', ' ', $field['label']));

            $type = isset($field['mage-type'])
                ? $field['mage-type']
                : $field['type'];

            if ($type == 'image') {
                $field['value'] = substr(
                    $field['value'],
                    strlen("url('"),
                    strpos("')", $field['value']) - strlen("')")
                );
            }

            $fieldAttributes = [
                'label'    => __($label),
                'title'    => __($label),
                'name'     => $id,
                'required' => false,
                'value'    => $field['value']
            ];

            if ($fieldSource = $this->_getFieldSource($fieldsetName, $field)) {
                $fieldAttributes['values'] = $fieldSource;
            }

            $fieldAttributes = $this->_addSpecialFieldTypesAttributes(
                $field['type'],
                $id,
                $fieldAttributes,
                $field['value']
            );

            if (isset($field['description'])) {
                $fieldAttributes['note'] = $field['description'];
            }

            $fieldset->addField(
                $id,
                $type,
                $fieldAttributes
            );
        }
    }

    /**
     * Add fieldsets from LESS
     *
     * @param \Magento\Framework\Data\Form $form
     * @return void
     */
    protected function _addFieldsetsFromLess($form)
    {
        $vars = $this->_themeCustomizerLess->parseFieldsLess();

        foreach ($vars as $section => $fields) {
            $sectionName = ucwords(str_replace('_', ' ', $section));

            $fieldset = $form->addFieldset($section, ['legend' => __($sectionName)]);
            $this->_addElementTypes($fieldset);

            $this->_addFieldsFromLess($fieldset, $fields);
        }
    }

    /**
     * Add color picker field init script
     *
     * @param string $id
     * @param string $color
     * @return string
     */
    protected function _addColorPickerScript($id, $color)
    {
        if (!$color) {
            $color = '#000';
        }

        $script = <<<EOF
    <script>
        require([
            'jquery',
            'ShopGo_ThemeCustomizer/js/spectrum'
        ], function($) {
            $('#{$id}').spectrum({
                color: '{$color}',
                theme: 'sp-dark',
                preferredFormat: 'hex6',
                showInput: true,
                showPalette: true,
                clickoutFiresChange: true,
                cancelText: '',
                palette: {$this->_colorPickerPalette}
            });
        });
    </script>
EOF;

        return $script;
    }

    /**
     * Set additional form field types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $element = 'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element\Image';
        return ['image' => $element];
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Customizer');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        $theme = $this->_coreRegistry->registry('current_theme')->getCode();
        return $this->_themeCustomizerHelper->isCustomizableTheme($theme);
    }
}
