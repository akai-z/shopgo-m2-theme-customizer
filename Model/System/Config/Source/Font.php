<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Model\System\Config\Source;

/**
 * Source model for fonts
 */
class Font
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('--Please Select--')],
            ['value' => 'font1', 'label' => __('Font 1')],
            ['value' => 'font2', 'label' => __('Font 2')],
            ['value' => 'font3', 'label' => __('Font 3')]
        ];
    }
}
