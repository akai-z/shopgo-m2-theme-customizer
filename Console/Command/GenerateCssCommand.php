<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ShopGo\ThemeCustomizer\Model\Less;

/**
 * Generate custom CSS file command
 */
class GenerateCssCommand extends Command
{
    /**
     * Theme argument
     */
    const THEME_ARGUMENT = 'theme';

    /**
     * @var Less
     */
    private $_less;

    /**
     * @var \Magento\Theme\Model\Theme
     */
    private $_theme;

    /**
     * @var \ShopGo\ThemeCustomizer\Helper\Data
     */
    private $_helper;

    /**
     * @param Less $less
     * @param \Magento\Theme\Model\Theme $theme
     * @param \ShopGo\ThemeCustomizer\Helper\Data $helper
     */
    public function __construct(
        Less $less,
        \Magento\Theme\Model\Theme $theme,
        \ShopGo\ThemeCustomizer\Helper\Data $helper
    ) {
        parent::__construct();
        $this->_less   = $less;
        $this->_theme  = $theme;
        $this->_helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('theme-customizer:generate-css')
            ->setDescription('Generate custom CSS command')
            ->setDefinition([
                new InputArgument(
                    self::THEME_ARGUMENT,
                    InputArgument::OPTIONAL,
                    'Theme'
                )
            ]);

        parent::configure();
    }

    /**
     * Generate CSS content
     *
     * @param string $theme
     * @return boolean
     */
    private function _generate($theme)
    {
        $this->_less->setTheme($theme);
        $this->_less->createDesignVarSymlink();

        $less = $this->_less->getFieldsCustomLessContent();
        $css  = $this->_less->getCssFromLess($less);

        return $this->_less->saveCustomCss($css);
    }

    /**
     * Generate CSS per vendor theme
     *
     * @param string $vendor
     * @return boolean
     */
    private function _generatePerVendorTheme($vendor)
    {
        $result = true;
        $themeCollection = $this->_theme->getCollection()->getItems();

        foreach ($themeCollection as $_theme) {
            $_vendor = substr($_theme->getCode(), 0, strpos($_theme->getCode(), '/'));

            if ($vendor == $_vendor) {
                $result = $result && $this->_generate($_theme->getCode());
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = true;
        $theme  = $input->getArgument(self::THEME_ARGUMENT);

        $this->_less->setAreaCode('adminhtml');

        if (!is_null($theme) && $theme != '*') {
            $themes = $this->_helper->getThemeCustomizerConfig($theme);
            $theme  = explode('/', $theme);

            if ($themes === false) {
                $result = false;
            } elseif ($themes && !empty($theme[1]) && $theme[1] != '*') {
                $result = $this->_generate(implode('/', $theme));
            } else {
                // Be careful!
                // This will generate CSS content for all themes under vendor.
                // Regardless whether they are theme customizer ready or not.
                $result = $this->_generatePerVendorTheme($theme[0]);
            }
        } else {
            $themeCollection = $this->_theme->getCollection()->getItems();

            foreach ($themeCollection as $_theme) {
                if ($this->_helper->isCustomizableTheme($_theme->getCode())) {
                    $result = $result && $this->_generate($_theme->getCode());
                }
            }
        }

        $result = $result
            ? 'Custom CSS file(s) has(have) been generated successfully!'
            : 'Failed to generate custom CSS file(s)';

        $output->writeln('<info>' . $result . '</info>');
    }
}
