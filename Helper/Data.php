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
     * Get theme customizer themes config
     *
     * @param string $theme
     * @return mixed
     */
    public function getThemeCustomizerConfig($theme = '')
    {
        $element = ['themes' => []];

        if ($theme && $theme != '*') {
            $theme = explode('/', $theme);
            $element['vendor'] = ['attributes' => ['id' => $theme[0]]];

            if (isset($theme[1]) && $theme[1] != '*') {
                $element['theme'] = ['attributes' => ['id' => $theme[1]]];
            }
        }

        $config = $this->_themeConfig->getConfigElement($element);

        if (!$config && isset($element['theme'])) {
            unset($element['theme']);

            $config = $this->_themeConfig->getConfigElement($element);
            if ($config && $config->hasChildNodes()) {
                $config = false;
            }
        }

        return $config;
    }

    /**
     * Check whether theme is customizable
     *
     * @param string $theme
     * @return boolean
     */
    public function isCustomizableTheme($theme)
    {
        return $this->getThemeCustomizerConfig($theme) ? true : false;
    }
}
