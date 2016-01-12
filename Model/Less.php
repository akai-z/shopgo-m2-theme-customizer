<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;

/**
 * Less model
 */
class Less extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Pub frontend path
     */
    const PUB_FRONTEND_PATH = 'frontend/ShopGo';

    /**
     * Var theme customizer path
     */
    const VAR_THEME_CUSTOMIZER_PATH = 'shopgo/theme_customizer';

    /**
     * Fields custom system config
     */
    const XPATH_CONFIG_THEMECUSTOMIZER_FIELDS_CUSTOM = 'theme_customizer/%sfields/custom';

    /**
     * Fields LESS file path
     */
    const FIELDS_FILE_PATH = 'customizer/_fields.less';

    /**
     * Fields custom LESS file path
     */
    const FIELDS_CUSTOM_FILE_PATH = '_fields_custom.less';

    /**
     * Custom theme CSS file path
     */
    const CUSTOM_CSS_FILE_PATH = 'css/theme.css';

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
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_varDirectoryReader;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_varDirectoryWriter;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_staticDirectoryWriter;

    /**
     * @var string
     */
    protected $_theme;

    /**
     * @var array
     */
    protected $_customLessArray;

    /**
     * @var \Magento\Config\Model\Config\Factory
     */
    protected $_configFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var string
     */
    private $_varSection;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Config\Model\Config\Factory $configFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Config\Model\Config\Factory $configFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        State $appState,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_filesystem    = $filesystem;
        $this->_configFactory = $configFactory;
        $this->messageManager = $messageManager;
        $this->_appState      = $appState;

        $this->_setVarDirectoryReader();
        $this->_setVarDirectoryWriter();
        $this->_setStaticDirectoryWriter();
    }

    /**
     * Set Var directory reader
     */
    protected function _setVarDirectoryReader()
    {
        $this->_varDirectoryReader = $this->_filesystem
            ->getDirectoryRead(DirectoryList::VAR_DIR);
    }

    /**
     * Set Var directory writer
     */
    protected function _setVarDirectoryWriter()
    {
         $this->_varDirectoryWriter = $this->_filesystem
            ->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * Set Static directory writer
     */
    protected function _setStaticDirectoryWriter()
    {
        $this->_staticDirectoryWriter = $this->_filesystem
            ->getDirectoryWrite(DirectoryList::STATIC_VIEW);
    }

    /**
     * Get static theme directory path
     *
     * @return string
     */
    protected function _getStaticThemeDirectoryPath()
    {
        return 'frontend/' . $this->_theme;
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
     * Get custom less file path
     *
     * @return string
     */
    protected function _getCustomLessFilePath()
    {
        return self::VAR_THEME_CUSTOMIZER_PATH . '/' . self::FIELDS_CUSTOM_FILE_PATH;
    }

    /**
     * Get static theme directory absolute path
     *
     * @return string
     */
    protected function _getStaticThemeDirectoryAbsolutePath()
    {
        return $this->_staticDirectoryWriter->getDriver()->getAbsolutePath(
            DirectoryList::PUB . '/' . DirectoryList::STATIC_VIEW . '/' . $this->_getStaticThemeDirectoryPath(),
            null, null
        );
    }

    /**
     * Get custom LESS file absolute path
     *
     * @return string
     */
    protected function _getCustomLessFileAbsolutePath()
    {
        return $this->_varDirectoryReader->getAbsolutePath($this->_getCustomLessFilePath());
    }

    /**
     * Get custom CSS file absolute path
     *
     * @param string $locale
     * @return string
     */
    protected function _getCustomCssFileAbsolutePath($locale)
    {
        return $this->_staticDirectoryWriter->getDriver()->getAbsolutePath(
            $this->_getStaticThemeDirectoryPath() . '/' . $locale . '/' . self::CUSTOM_CSS_FILE_PATH,
            null, null
        );
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
     * Check whether fields LESS file exists
     *
     * @return boolean
     */
    protected function _fieldsFileExists()
    {
        return $this->_varDirectoryReader->isFile(
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
        return $this->_varDirectoryReader->readFile(
            $this->_getVarThemeCustomizerDirectory()
            . '/' . self::DESIGN_FIELDS_FILE_PATH
        );
    }

    /**
     * Check whether fields source LESS file exists
     *
     * @return boolean
     */
    protected function _fieldsSourceFileExists()
    {
        return $this->_varDirectoryReader->isFile(
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
        return $this->_varDirectoryReader->readFile(
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
     * Get LESS variable valid attributes
     *
     * @return array
     */
    protected function _getLessVarValidAttributes()
    {
        return [
            'description', 'mage-type'
        ];
    }

    /**
     * Get LESS variable attribute value
     *
     * @param string $data
     * @param string $label
     * @return string
     */
    protected function _getLessVarAttributeValue($data, $label)
    {
        return trim(substr(
            $data, strlen("//{$label}:"), strlen($data)
        ));
    }

    /**
     * Get LESS variable attribute code
     *
     * @param string $attribute
     * @return string
     */
    protected function _getLessVarAttributeCode($attribute)
    {
        $result = '';

        foreach ($this->_getLessVarValidAttributes() as $_attribute) {
            if (strpos($attribute, $_attribute) !== false) {
                $result = $_attribute;
                break;
            }
        }

        return $result;
    }

    /**
     * Get LESS variable attributes
     *
     * @param array $var
     * @param array $rawData
     * @return array
     */
    protected function _getLessVarAttributes($var, $rawData)
    {
        foreach ($rawData as $data) {
            if ($attribute = $this->_getLessVarAttributeCode($data)) {
                $var[$attribute] = $this->_getLessVarAttributeValue($data, $attribute);
            }
        }

        return $var;
    }

    /**
     * Get fields LESS variable parts
     *
     * @param string $var
     * @param boolean $skipCustom
     * @return array
     */
    protected function _getVarParts($var, $skipCustom = false)
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
            $parts = $this->_getLessVarAttributes(
                $parts,
                array_slice($_parts, 0, -1) // Remove the last element of the array
            );
        }

        $value = rtrim($_varVal[1], ';');

        // Section-Type-Label
        $customLessVar = "{$_var[0]}-{$_var[1]}-{$_var[2]}";

        if (!$skipCustom && isset($this->_customLessArray[$customLessVar])) {
            $value = $this->_customLessArray[$customLessVar];
        }

        $parts['type']  = $_var[1];
        $parts['label'] = $_var[2];
        $parts['value'] = trim($value, '"');

        $this->_varSection = $_var[0];

        return $parts;
    }

    /**
     * Get custom less variables as array
     *
     * @return array
     */
    protected function _getCustomLessVarArray()
    {
        $less = [];
        $raw  = $this->getFieldsCustomLessContent();

        if (empty($raw)) {
            return $less;
        }
        if (empty($raw[0])) {
            return $less;
        }

        $raw = str_replace('@', '', $raw);
        $raw = array_map('trim', explode(self::LESS_VAR_SEPARATOR, $raw));

        foreach ($raw as $var) {
            $_var = array_map('trim', explode(':', $var));
            $less[$_var[0]] = rtrim($_var[1], ';');
        }

        return $less;
    }

    /**
     * Parse fields LESS file
     *
     * @return array
     */
    public function parseFieldsLess()
    {
        if (!$this->_fieldsFileExists()) {
            return [];
        }

        $less = $this->_getFieldsLessContent();
        $this->_customLessArray = $this->_getCustomLessVarArray();

        return $this->_getFieldsSections($less);
    }

    /**
     * Parse fields source LESS file
     *
     * @return array
     */
    public function parseFieldsSourceLess()
    {
        if (!$this->_fieldsSourceFileExists()) {
            return [];
        }

        $less  = $this->_getFieldsSourceLessContent();
        $_vars = array_map('trim', explode(self::LESS_VAR_SEPARATOR, $less));
        $vars  = [];

        foreach ($_vars as $var) {
            $varParts = $this->_getVarParts($var, true);

            if (!empty($varParts)) {
                $values = [];
                $value = rtrim($varParts['value'], ';');
                $value = array_map('trim', explode('|', $value));

                foreach ($value as $val) {
                    $values[$val] = $val;
                }

                $varParts['value'] = $values;

                $vars[$this->_varSection][] = $varParts;
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
        $theme = str_replace('/', '_', $this->_theme);

        return $this->_getConfigData(
            sprintf(self::XPATH_CONFIG_THEMECUSTOMIZER_FIELDS_CUSTOM, $theme . '_')
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
            $theme = str_replace('/', '_', $this->_theme);

            $group = [
                $theme . '_fields' => [
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
