<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Less model
 */
class Less extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Design frontend path
     */
    #const DESIGN_FRONTEND_PATH = 'design/frontend/Shopgo';

    /**
     * Pub frontend path
     */
    const PUB_FRONTEND_PATH = 'frontend/ShopGo';

    /**
     * Design frontend fields LESS file path
     */
    #const DESIGN_FIELDS_FILE_PATH = 'frontend/ShopGo';

    /**
     * Var theme customizer path
     */
    const VAR_THEME_CUSTOMIZER_PATH = 'shopgo/theme_customizer';

    /**
     * Pub static frontend fields LESS file path
     */
    #const PUB_FIELDS_FILE_PATH = 'frontend/ShopGo';

    /**
     * Fields custom system config
     */
    const XPATH_CONFIG_THEMECUSTOMIZER_FIELDS_CUSTOM = 'theme_customizer/fields/custom';

    /**
     * Fields LESS file path
     */
    const FIELDS_FILE_PATH = 'customizer/_fields.less';

    /**
     * Fields custom LESS file path
     */
    #const FIELDS_CUSTOM_FILE_PATH = '_fields_custom.less';

    /**
     * Var source LESS file path
     */
    const VAR_SOURCE_FILE_PATH = 'customizer/_var_source.less';

    /**
     * Fields LESS section separator
     */
    const FIELDS_SECTION_SEPARATOR = '//========';

    /**
     * LESS var separator
     */
    const LESS_VAR_SEPARATOR = '//---';

    /**
     * Fields LESS var child separator
     */
    const FIELDS_VAR_CHILD_SEPARATOR = '//>';

    /**
     * LESS var description label
     */
    const LESS_VAR_DESCRIPTION_LABEL = '//Description:';

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_varDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    #protected $_designFrontendDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    #protected $_pubFrontendDirectory;

    /**
     * @var string
     */
    protected $_theme;

    /**
     * @var \Magento\Config\Model\Config\Factory
     */
    protected $_configFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var string
     */
    private $_varSection;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Config\Model\Config\Factory $configFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Config\Model\Config\Factory $configFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_filesystem    = $filesystem;
        $this->_configFactory = $configFactory;
        $this->_scopeConfig   = $scopeConfig;
        $this->messageManager = $messageManager;

        $this->_setVarDirectory();

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
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
     * Get var theme customizer directory
     *
     * @return string
     */
    protected function _getVarThemeCustomizerDirectory()
    {
        return self::VAR_THEME_CUSTOMIZER_PATH . '/' . $this->_theme;
    }

    /**
     * Get config model
     *
     * @param array $configData
     * @return \Magento\Config\Model\Config
     */
    protected function _getConfigModel($configData = [])
    {
        /** @var \Magento\Config\Model\Config $configModel  */
        $configModel = $this->_configFactory->create(['data' => $configData]);
        return $configModel;
    }

    /**
     * Get config data value
     *
     * @param string $path
     * @return string
     */
    protected function _getConfigData($path)
    {
        return $this->_getConfigModel()->getConfigDataValue($path);
    }

    /**
     * Get config model
     *
     * @param array $configData
     */
    protected function _setConfigData($configData = [])
    {
        $this->_getConfigModel($configData)->save();
    }

    /**
     * Set design frontend directory
     */
    /*protected function _setDesignFrontendDirectory()
    {
        $this->_designFrontendDirectory = $this->_filesystem
            ->getDirectoryRead(DirectoryList::APP . self::DESIGN_FRONTEND_PATH);
    }*/

    /**
     * Set pub frontend directory
     */
    /*protected function _setPubFrontendDirectory()
    {
        $this->_pubFrontendDirectory = $this->_filesystem
            ->getDirectoryRead(DirectoryList::STATIC_VIEW . self::PUB_FRONTEND_PATH);
    }*/

    /**
     * Check whether fields LESS file exists
     *
     * @return boolean
     */
    protected function _fieldsFileExists()
    {
        return $this->_varDirectory->isFile(
            $this->_getVarThemeCustomizerDirectory()
            . '/' . self::FIELDS_FILE_PATH
        );
    }

    /**
     * Get fields LESS file content
     *
     * @return boolean
     */
    protected function _getFieldsLessContent()
    {
        return $this->_varDirectory->readFile(
            $this->_getVarThemeCustomizerDirectory()
            . '/' . self::DESIGN_FIELDS_FILE_PATH
        );
    }

    /**
     * Check whether fields custom LESS file exists
     *
     * @return boolean
     */
    /*protected function _fieldsCustomFileExists()
    {
        return $this->_varDirectory->isFile(
            $this->_getVarThemeCustomizerDirectory()
            . '/' . self::FIELDS_CUSTOM_FILE_PATH
        );
    }*/

    /**
     * Get fields custom LESS file content
     *
     * @return string
     */
    /*protected function _getFieldsCustomLessContent()
    {
        return $this->_varDirectory->readFile(
            $this->_getVarThemeCustomizerDirectory()
            . '/' . self::FIELDS_CUSTOM_FILE_PATH
        );
    }*/

    /**
     * Check whether fields source LESS file exists
     *
     * @return boolean
     */
    protected function _fieldsSourceFileExists()
    {
        return $this->_varDirectory->isFile(
            $this->_getVarThemeCustomizerDirectory()
            . '/' . self::FIELDS_SOURCE_FILE_PATH
        );
    }

    /**
     * Get fields source LESS file content
     *
     * @return string
     */
    protected function _getFieldsSourceLessContent()
    {
        return $this->_varDirectory->readFile(
            $this->_getVarThemeCustomizerDirectory()
            . '/' . self::FIELDS_Source_FILE_PATH
        );
    }

    /**
     * Get fields LESS variables sections
     *
     * @param string $fields
     * @return array
     */
    protected function _getFieldsSections($fields)
    {
        $sections  = [];
        $_sections = array_map('trim', explode(self::FIELDS_SECTION_SEPARATOR, $fields));

        foreach ($_sections as $section) {
            if (empty($section)) {
                continue;
            }

            $_section = $this->_getFieldsVars($section);
            $sections[$this->_varSection] = $_section;
        }

        return $sections;
    }

    /**
     * Get fields LESS variables
     *
     * @param string $_vars
     * @param string $varSeparator
     * @param string $childSeparator
     * @return array
     */
    protected function _getFieldsVars(
        $_vars,
        $varSeparator = self::LESS_VAR_SEPARATOR,
        $childSeparator = self::FIELDS_VAR_CHILD_SEPARATOR
    ) {
        $vars  = [];
        $_vars = array_map('trim', explode($varSeparator, $_vars));

        foreach ($_vars as $index => $var) {
            if (empty($var)) {
                continue;
            }

            $vars[$index] = [];

            if (strpos($var, $childSeparator) !== false) {
                $subVars = array_map('trim', explode($childSeparator, $var));
                $varParts = $this->_getVarParts($subVars[0]);

                if (empty($varParts)) {
                    continue;
                }

                if (strpos($subVars[1], $childSeparator) === false) {
                    $varSeparator = PHP_EOL;
                }

                $vars[$index]['children'] = $this->_getFieldsVars(
                    $subVars[1],
                    $varSeparator,
                    $childSeparator . '>'
                );
            } else {
                $varParts = $this->_getVarParts($var);

                if (empty($varParts)) {
                    continue;
                }
            }

            $vars[$index] = array_merge($vars[$index], $varParts);
        }

        return $vars;
    }

    /**
     * Get fields LESS variable parts
     *
     * @param string $var
     * @return array
     */
    protected function _getVarParts($var)
    {
        $parts  = [];
        $_parts = array_map('trim', explode(PHP_EOL, $var));

        if (empty($_parts)) {
            return $parts;
        }

        $partsCount = count($_parts);

        $_varVal = array_map('trim', explode(':', $_parts[$partsCount - 1]));
        $_var    = explode('-', ltrim($_varVal[0], '@'));

        if (count($_var) < 3) {
            return $parts;
        }

        if ($partsCount > 1) {
            $parts['description'] = trim(substr(
                $_parts[0],
                strlen(self::LESS_VAR_DESCRIPTION_LABEL),
                strlen($_parts[0])
            ));
        }

        $parts['type']  = $_var[1];
        $parts['label'] = $_var[2];
        $parts['value'] = $_varVal[1];

        $this->_varSection = $_var[0];

        return $parts;
    }

    /**
     * Parse fields LESS file
     *
     * @return array
     */
    public function parseFieldsLess()
    {
        //if (!$this->_fieldsFileExists()) {
            //return [];
        //}

        $less = $this->_getFieldsLessContent();

        //if ($lessCustom = $this->_getFieldsCustomLessContent()) {
            //$less = arra_merge($less, $lessCustom);
        //}

        return $this->_getFieldsSections($less);
    }

    /**
     * Parse fields source LESS file
     *
     * @return array
     */
    public function parseFieldsSourceLess()
    {
        //if (!$this->_fieldsSourceLessFileExists()) {
            //return [];
        //}

        $less  = $this->_getFieldsSourceLessContent();
        $_vars = array_map('trim', explode(self::LESS_VAR_SEPARATOR, $less));
        $vars  = [];

        foreach ($_vars as $var) {
            $varParts = $this->_getVarParts($var);

            if (!empty($varParts)) {
                $vars[$this->_varSection] = $varParts;
            }
        }

        return $vars;
    }

    /**
     * Get fields custom less content
     *
     * @return string
     */
    public function getFieldsCustomLessContent()
    {
        return $this->_getConfigData(
            self::XPATH_CONFIG_THEMECUSTOMIZER_FIELDS_CUSTOM
        );
    }

    /**
     * Set fields custom less content
     *
     * @param string $content
     */
    public function setFieldsCustomLessContent($content)
    {
        try {
            $group = [
                'fields' => [
                    'fields' => [
                        'custom' => [
                            'value' => $content
                        ]
                    ]
                ]
            ];

            $configData = [
                'section' => 'theme_customizer',
                'website' => null,
                'store'   => null,
                'groups'  => $group
            ];

            $this->_setConfigData($configData);

            $this->messageManager->addSuccess(__('You saved the configuration.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $messages = explode("\n", $e->getMessage());
            foreach ($messages as $message) {
                $this->messageManager->addError($message);
            }
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Something went wrong while saving this configuration:') . ' ' . $e->getMessage()
            );
        }
    }

    /**
     * Set theme
     *
     * @param string $theme
     */
    public function setTheme($theme)
    {
        $this->_theme = $theme;
    }
}
