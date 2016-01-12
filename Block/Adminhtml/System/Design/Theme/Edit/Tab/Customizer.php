<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Block\Adminhtml\System\Design\Theme\Edit\Tab;

use Magento\Framework\App\Area;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Theme\Model\Theme\Collection;

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
     * @var \ShopGo\ThemeCustomizer\Helper\Data
     */
    protected $_themeCustomizerHelper;

    /**
     * @var \ShopGo\ThemeCustomizer\Model\Source\ColorPickerPalette
     */
    protected $_colorPickerPalette;

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
        array $data = []
    ) {
        $this->_fileSize = $fileSize;
        $this->_themeCustomizerHelper = $themeCustomizerHelper;
        $this->_colorPickerPalette = $colorPickerPalette->getPalette();
        parent::__construct($context, $registry, $formFactory, $objectManager, $data);
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

        $form->setFieldNameSuffix('theme');
        $this->setForm($form);

        return $this;
    }

    /**
     * Add color picker field init script
     *
     * @param string $id
     * @param string $color
     * @return string
     */
    protected function _addColorPickerInitScript($id, $color = '#000')
    {
        $script = <<<EOF
    <script>
        require([
            'jquery',
            'ShopGo_ThemeCustomizer/js/spectrum'
        ], function($) {
            $('#{$id}').spectrum({
                color: '{$value}',
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
