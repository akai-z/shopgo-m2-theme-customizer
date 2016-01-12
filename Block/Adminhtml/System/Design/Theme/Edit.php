<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Theme editor container
 */
namespace ShopGo\ThemeCustomizer\Block\Adminhtml\System\Design\Theme;

use \Magento\Backend\Block\Widget\Form\Container;

class Edit extends \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit
{
    /**
     * @var \ShopGo\ThemeCustomizer\Helper\Data
     */
    protected $_themeCustomizerHelper;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \ShopGo\ThemeCustomizer\Helper\Data $themeCustomizerHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);

        $this->_themeCustomizerHelper = $themeCustomizerHelper;
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_blockGroup = 'Magento_Theme';
        $this->_controller = 'Adminhtml_System_Design_Theme';
        $this->setId('theme_edit');

        if (is_object($this->getLayout()->getBlock('page.title'))) {
            $this->getLayout()->getBlock('page.title')->setPageTitle($this->getHeaderText());
        }

        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = $this->_getCurrentTheme();
        if ($theme) {
            if (!$this->_themeCustomizerHelper->isCustomizableTheme($theme->getCode())) {
                if ($theme->isEditable()) {
                    $this->buttonList->add(
                        'save_and_continue',
                        [
                            'label' => __('Save and Continue Edit'),
                            'class' => 'save',
                            'data_attribute' => [
                                'mage-init' => [
                                    'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                                ],
                            ]
                        ],
                        1
                    );
                } else {
                    $this->buttonList->remove('save');
                    $this->buttonList->remove('reset');
                }
            } else {
                 $this->buttonList->add(
                     'save_and_continue',
                     [
                         'label' => __('Save and Continue Edit'),
                         'class' => 'save',
                         'data_attribute' => [
                             'mage-init' => [
                                 'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                             ],
                         ]
                     ],
                     1
                 );

                $this->buttonList->remove('reset');
             }

            if ($theme->isDeletable()) {
                if ($theme->hasChildThemes()) {
                    $message = __('Are you sure you want to delete this theme?');
                    $onClick = sprintf(
                        "deleteConfirm('%s', '%s')",
                        $message,
                        $this->getUrl('adminhtml/*/delete', ['id' => $theme->getId()])
                    );
                    $this->buttonList->update('delete', 'onclick', $onClick);
                }
            } else {
                $this->buttonList->remove('delete');
            }
        }

        return Container::_prepareLayout();
    }
}
