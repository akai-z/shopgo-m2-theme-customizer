<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Model\Config;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Configuration reader model
 */
class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * Vendor config directory path
     */
    const VENDOR_CONFIG_DIRECTORY_PATH = 'vendor/shopgo/theme-customizer-config/';

    /**
     * Var config directory path
     */
    const VAR_CONFIG_DIRECTORY_PATH = 'shopgo/theme_customizer/';

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_rootDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_varDirectory;

    /**
     * @var string
     */
    protected $_vendorConfigFile;

    /**
     * @var string
     */
    protected $_varConfigFile;

    /**
     * @var string
     */
    protected $_fileName;

    /**
     * @var string
     */
    protected $_schemaFile;

    /**
     * @var \DomDocument
     */
    protected $_dom;

    /**
     * @var \DOMXPath
     */
    protected $_domXpath;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Config\FileResolverInterface $fileResolver
     * @param \Magento\Config\Model\Config\Structure\Converter $converter
     * @param \ShopGo\ThemeCustomizer\Model\Config\SchemaLocator $schemaLocator
     * @param \Magento\Framework\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Config\Model\Config\Structure\Converter $converter,
        \ShopGo\ThemeCustomizer\Model\Config\SchemaLocator $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $fileName = 'themes.xml',
        $idAttributes = [],
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'global'
    ) {
        $this->_filesystem = $filesystem;
        $this->_fileName   = $fileName;
        $this->validationState = $validationState;

        $this->_setRootDirectory();
        $this->_setVarDirectory();

        if ($this->_configFileExists()) {
            $this->_setDom();
            $this->_setDomXpath();
        }

        $this->_schemaFile = $schemaLocator->getSchema();

        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }

    /**
     * Set Root directory
     */
    protected function _setRootDirectory()
    {
        $this->_rootDirectory = $this->_filesystem
            ->getDirectoryRead(DirectoryList::ROOT);
    }

    /**
     * Set Var directory
     */
    protected function _setVarDirectory()
    {
        $this->_varDirectory = $this->_filesystem
            ->getDirectoryRead(DirectoryList::VAR_DIR);
    }

    /**
     * Get Vendor config file absolute path
     *
     * @return string
     */
    protected function _getVendorConfigFileAbsolutePath()
    {
        return $this->_rootDirectory->getAbsolutePath(
            self::VENDOR_CONFIG_DIRECTORY_PATH . $this->_fileName
        );
    }

    /**
     * Get Var config file absolute path
     *
     * @return string
     */
    protected function _getVarConfigFileAbsolutePath()
    {
        return $this->_varDirectory->getAbsolutePath(
            self::VAR_CONFIG_DIRECTORY_PATH . $this->_fileName
        );
    }

    /**
     * Get config file absolute path
     *
     * @return string
     */
    protected function _getConfigFileXmlContent()
    {
        $config = '';

        if ($this->_vendorConfigFile) {
            $config = $this->_rootDirectory->readFile(
                self::VENDOR_CONFIG_DIRECTORY_PATH . $this->_fileName
            );
        }

        if (!$config && $this->_varConfigFile) {
            $config = $this->_varDirectory->readFile(
                self::VAR_CONFIG_DIRECTORY_PATH . $this->_fileName
            );
        }

        return $config;
    }

    /**
     * Set DOM
     */
    protected function _setDom()
    {
        $this->_dom = new \DOMDocument();
        $this->_dom->preserveWhiteSpace = false;
        $this->_dom->loadXML($this->_getConfigFileXmlContent());
    }

    /**
     * Set DOM XPath
     */
    protected function _setDomXpath()
    {
        $this->_domXpath = new \DOMXPath($this->_dom);
    }

    /**
     * Check whether config file exists
     *
     * @return boolean
     */
    protected function _configFileExists()
    {
        $this->_vendorConfigFile = $this->_rootDirectory->isFile(
            self::VENDOR_CONFIG_DIRECTORY_PATH . $this->_fileName
        );

        $this->_varConfigFile = $this->_varDirectory->isFile(
            self::VAR_CONFIG_DIRECTORY_PATH . $this->_fileName
        );

        return $this->_vendorConfigFile || $this->_varConfigFile;
    }

    /**
     * Validate DOM
     *
     * @return boolean
     */
    protected function _validateDom()
    {
        $result = true;

        if ($this->validationState->isValidationRequired() && $this->_schemaFile) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get DOM XPath value
     *
     * @param string $xpath
     * @return string
     */
    protected function _getDomXpathValue($xpath)
    {
        return $this->_domXpath->query($xpath);
    }

    /**
     * Get config xpath
     *
     * @param array $element
     * @return string
     */
    protected function _getConfigXpath($element)
    {
        $xpath = '/';

        foreach ($element as $_element => $data) {
            $attributesText = '';
            $valueText = '';

            switch (true) {
                case isset($data['attributes']):
                    foreach ($data['attributes'] as $attrKey => $attrVal) {
                        $attributesText .= '[@' . $attrKey . '="' . $attrVal . '"]';
                    }
                    break;
                case isset($data['value']):
                    $valueText .= '[.="' . $data['value'] . '"]';
                    break;
            }

            $xpath .= '/' . $_element . $attributesText . $valueText;
        }

        return $xpath;
    }

    /**
     * Get config element
     *
     * @param array $element
     * @return string|null
     */
    public function getConfigElement($element)
    {
        if (!$this->_configFileExists() || !$this->_validateDom()) {
            return null;
        }

        $element = $this->_getDomXpathValue($this->_getConfigXpath($element));

        return $element->item(0) !== null ? $element->item(0) : null;
    }
}
