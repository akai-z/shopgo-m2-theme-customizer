<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
     /**
     * @var \ShopGo\ThemeCustomizer\Model\Source\Theme
     */
    protected $_themes;

    /**
     * @param \Magento\Framework\App\Helper\Context $context,
     * @param \ShopGo\ThemeCustomizer\Model\Source\Theme $theme
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \ShopGo\ThemeCustomizer\Model\Source\Theme $theme
    ) {
        $this->_themes = $theme->toOptionArray();
        parent::__construct($context);
    }

    /**
     * Check whether theme is customizable
     *
     * @param string $theme
     * @return $this
     */
    public function isCustomizableTheme($theme)
    {
        $result = false;

        foreach ($this->_themes as $_theme) {
            if ($theme == $_theme['value']) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
