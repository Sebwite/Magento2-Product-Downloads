<?php namespace Sebwite\ProductDownloads\Block\Product;

use Magento\Framework\View\Element\Template;
use Sebwite\ProductDownloads\Model\Download;

/**
 * Class:Downloads
 * Sebwite\ProductDownloads\Block
 *
 * @author      Sebwite
 * @package     Sebwite\ProductDownloads
 * @copyright   Copyright (c) 2015, Sebwite. All rights reserved
 */
class Downloads extends Template
{

    /**
     * @var Download
     */
    private $download;
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param Download                                         $download
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Registry $coreRegistry, Download $download)
    {
        $this->download = $download;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);

        $this->setTabTitle();
    }

    /**
     * Set tab title
     *
     * @return void
     */
    public function setTabTitle()
    {
        $this->setTitle(__('Downloads'));
    }

    /**
     * Return Downloads
     *
     * @return mixed
     */
    public function getDownloads()
    {
        $product = $this->getProduct();
        return $this->download->getDownloadsForProductInStore($product->getId(), $product->getStoreId());
    }

    /**
     * Return current product instance
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * @param $download
     *
     * @return string
     */
    public function getDownloadUrl($download)
    {
        // @TODO - index.php weghalen
        return str_replace('index.php', '', $this->getBaseUrl()) . $this->download->getUrl($download);
    }
}
