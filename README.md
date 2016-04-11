Theme Customizer
================


#### Contents
*   [Synopsis](#syn)
*   [Overview](#over)
*   [Installation](#install)
*   [Tests](#tests)
*   [Contributors](#contrib)
*   [License](#lic)


## <a name="syn"></a>Synopsis

A module that allows users to customize their themes.

## <a name="over"></a>Overview

Theme Customizer module allows store admins to customize some of the frontend themes elements that are predefined by themes developers.
The module uses LESS in order to achieve that.

## <a name="install"></a>Installation

Below, you can find two ways to install the theme customizer module. With the release of Magento 2.0, you'll also be able to install modules using the Magento Marketplaces.

### 1. Install via Composer (Recommended)
First, make sure that Composer is installed: https://getcomposer.org/doc/00-intro.md

Make sure that Packagist repository is not disabled.

Run Composer require to install the module:

    php <your Composer install dir>/composer.phar require shopgo/theme-customizer:~1.0

### 2. Clone the theme-customizer repository
Clone the <a href="https://bitbucket.org/shopgo-magento2/theme-customizer" target="_blank">theme-customizer</a> repository using either the HTTPS or SSH protocols.

### 2.1. Copy the code
Create a directory for the theme customizer module and copy the cloned repository contents to it:

    mkdir -p <your Magento install dir>/app/code/ShopGo/ThemeCustomizer
    cp -R <theme-customizer clone dir>/* <your Magento install dir>/app/code/ShopGo/ThemeCustomizer

### Update the Magento database and schema
If you added the module to an existing Magento installation, run the following command:

    php <your Magento install dir>/bin/magento setup:upgrade

### Verify the module is installed and enabled
Enter the following command:

    php <your Magento install dir>/bin/magento module:status

The following confirms you installed the module correctly, and that it's enabled:

    example
        List of enabled modules:
        ...
        ShopGo_ThemeCustomizer
        ...

## <a name="tests"></a>Tests

TODO

## <a name="contrib"></a>Contributors

Ammar (<ammar@shopgo.me>)

## <a name="lic"></a>License

[Open Source License](LICENSE.txt)
