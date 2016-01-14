# README #

Magento 2.0 Product Downloads extension

Adds a new tab on the product page to add downloads. This downloads will be shown an a product page.

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

##Todo's:
* Add config page (for setting alowed extensions)