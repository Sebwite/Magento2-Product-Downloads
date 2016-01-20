# README #

Magento 2.0 Product Downloads extension
![Alt text](header.jpg?raw=true "Magento Product Downloads")

Adds a new tab on the product page to add downloads. This downloads will be shown in a tab on the product page. You can configure the allowed file extensions from the admin settings menu.

## Screenshot
![Alt text](screenshot.png?raw=true "Magento 2 Product downloads extension")

## Installation with composer (recommended)
* Include the repository: `composer require sebwite/magento2-product-downloads`
* Enable the extension: `php bin/magento --clear-static-content module:enable Sebwite_ProductDownloads`
* Upgrade db scheme: `php bin/magento setup:upgrade`
* Clear cache

## Installation without composer
* Download zip file of this extension
* Place all the files of the extension in your Magento 2 installation in the folder app/code/Sebwite/ProductDownloads
* Enable the extension: `php bin/magento --clear-static-content module:enable Sebwite_ProductDownloads`
* Upgrade db scheme: `php bin/magento setup:upgrade`
* Clear cache

After these steps the extra tab on the products page will be visible. This extension has no config options (yet)

---
[![Alt text](https://www.sebwite.nl/wp-content/themes/sebwite/assets/images/logo-sebwite.png "Sebwite.nl")](https://sebwite.nl)