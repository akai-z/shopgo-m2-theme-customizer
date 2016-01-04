<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Model\Source;

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
            ['value' => 'ShopGo/x', 'label' => __('X')],
            ['value' => 'Magento/luma', 'label' => __('Luma')]
        ];
    }
}
