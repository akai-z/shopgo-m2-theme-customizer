<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Model\System\Config\Source;

/**
 * Source model for Theme Customizer themes
 */
class Theme
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('--Please Select--')],
            ['value' => 'theme1', 'label' => __('Theme 1')],
            ['value' => 'theme2', 'label' => __('Theme 2')],
            ['value' => 'theme3', 'label' => __('Theme 3')]
        ];
    }
}
