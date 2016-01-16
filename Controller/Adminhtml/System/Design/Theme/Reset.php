<?php
/**
 *
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Controller\Adminhtml\System\Design\Theme;

class Reset extends \Magento\Theme\Controller\Adminhtml\System\Design\Theme
{
    /**
     * @var \Magento\Framework\View\Design\Theme\FlyweightFactory
     */
    protected $_themeFactory;

    /**
     * @var \ShopGo\ThemeCustomizer\Model\Less
     */
    protected $_themeCustomizerLess;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\Filesystem $appFileSystem
     * @param \Magento\Framework\View\Design\Theme\FlyweightFactory $themeFactory
     * @param \ShopGo\ThemeCustomizer\Model\Less $themeCustomizerLess
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Filesystem $appFileSystem,
        \Magento\Framework\View\Design\Theme\FlyweightFactory $themeFactory,
        \ShopGo\ThemeCustomizer\Model\Less $themeCustomizerLess
    ) {
        parent::__construct($context, $coreRegistry, $fileFactory, $assetRepo, $appFileSystem);

        $this->_themeFactory = $themeFactory;
        $this->_themeCustomizerLess = $themeCustomizerLess;
    }

    /**
     * Reset action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $theme = $this->_themeFactory->create($id);

        $this->_themeCustomizerLess->setTheme($theme->getCode());
        $this->_themeCustomizerLess->setFieldsCustomLessContent('');
        $this->_themeCustomizerLess->deleteCustomLessFile();
        $this->_themeCustomizerLess->deleteCustomCss();

        $this->_redirect('adminhtml/*/edit', ['id' => $theme->getId()]);
    }
}
