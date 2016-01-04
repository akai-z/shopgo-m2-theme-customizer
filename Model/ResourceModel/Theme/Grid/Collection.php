<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Model\ResourceModel\Theme\Grid;

/**
 * Theme grid collection
 */
class Collection extends \Magento\Theme\Model\ResourceModel\Theme\Grid\Collection
{
    /**
     * Add area filter
     *
     * @return \Magento\Theme\Model\ResourceModel\Theme\Collection
     */
    protected function _initSelect()
    {
        \Magento\Theme\Model\ResourceModel\Theme\Collection::_initSelect();
        $this->filterVisibleThemes()->addAreaFilter(\Magento\Framework\App\Area::AREA_FRONTEND)->addParentTitle();
        $this->_hideThemes();
        return $this;
    }

    protected function _hideThemes()
    {
        $hiddenThemes = $this->_getHiddenThemes();
        $themesFilter = [];

        if (!empty($hiddenThemes)) {
            foreach ($hiddenThemes as $hiddenTheme) {
                $themesFilter[] = ['neq' => $hiddenTheme];
            }
        }

        $this->addFieldToFilter('main_table.code', $themesFilter);
    }

    protected function _getHiddenThemes()
    {
        return [
            'Magento/blank'
        ];
    }
}
