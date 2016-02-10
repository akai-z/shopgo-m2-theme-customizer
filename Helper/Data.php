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
     * @var \ShopGo\ThemeCustomizer\Model\Config\Reader
     */
    protected $_themeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \ShopGo\ThemeCustomizer\Model\Config\Reader $themeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \ShopGo\ThemeCustomizer\Model\Config\Reader $themeConfig
    ) {
        $this->_themeConfig = $themeConfig;
        parent::__construct($context);
    }

    /**
     * Check whether theme is customizable
     *
     * @param string $theme
     * @return boolean
     */
    public function isCustomizableTheme($theme)
    {
        $result = false;
        $theme  = explode('/', $theme);
        $themes = $this->_themeConfig->getConfigElement(['themes' => []]);

        if (!$themes) {
            return $result;
        }
        if (!$themes->hasChildNodes()) {
            return $result;
        }

        foreach ($themes->childNodes as $vendor) {
            if ($vendor->getAttribute('id') == $theme[0]) {
                if ($vendor->hasChildNodes()) {
                    foreach ($vendor->childNodes as $_theme) {
                        if ($_theme->getAttribute('id') == $theme[1]) {
                            return true;
                        }
                    }
                } else {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }
}
