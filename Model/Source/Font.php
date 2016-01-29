<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Model\Source;

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
            'en' => [
                'Roboto Slab, Helvetica Neue, Helvetica, Arial, sans-serif',
                'Robotos Slab, Helvetica Neue, Helvetica, Arial, sans-serif'
            ],
            'ar' => [
                'Roboto Slab, Helvetica Neue, Helvetica, Arial, sans-serif',
                'Robotos Slab, Helvetica Neue, Helvetica, Arial, sans-serif'
            ]
        ];
    }
}
