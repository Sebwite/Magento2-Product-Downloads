<?php namespace Sebwite\ProductDownloads\Block\Adminhtml\Product\Edit\Tab;

/**
 * Class:Download
 * Sebwite\ProductDownloads\Block\Adminhtml\Product\Edit\Tab
 *
 * @author      Sebwite
 * @package     Sebwite\ProductDownloads
 * @copyright   Copyright (c) 2016, Sebwite. All rights reserved
 */
class Download extends \Magento\Backend\Block\Widget
{

    /**
     * Delete URL
     */
    const URL_PATH_DELETE = 'downloads/delete';
    /**
     * @var string
     */
    protected $_template = 'product/tab/download.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    protected $defaultMimeTypes = ['pdf', 'docx', 'jpg', 'jpeg', 'png'];
    /**
     * @var \Sebwite\ProductDownloads\Model\Download
     */
    private $download;

    /**
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Framework\Registry              $coreRegistry
     * @param \Sebwite\ProductDownloads\Model\Download $download
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Sebwite\ProductDownloads\Model\Download $download,
        array $data = []
    )
    {
        $this->coreRegistry = $coreRegistry;
        $this->download = $download;

        parent::__construct($context, $data);
    }

    /**
     * Is readonly stock
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getProduct()->getInventoryReadonly();
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
     * @return bool
     */
    public function isNew()
    {
        if ($this->getProduct()->getId()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getFieldSuffix()
    {
        return 'product';
    }

    /**
     * Check if product type is virtual
     *
     * @return bool
     */
    public function isVirtual()
    {
        return $this->getProduct()->getIsVirtual();
    }

    /**
     * Get product downloads
     *
     * @return mixed
     */
    public function getDownloads()
    {
        $product = $this->getProduct();
        return $this->download->getResource()->getDownloadsForProductInStore($product->getId(), $product->getStoreId(), false);
    }

    /**
     * Get Download url
     *
     * @param $download
     *
     * @return mixed
     */
    public function getDownloadUrl($download)
    {
        return $this->getBaseUrl() . $this->download->getUrl($download);
    }

    /**
     * @param $downloadID
     *
     * @return string
     */
    public function getDeleteUrl($downloadID)
    {
        return $this->_urlBuilder->getUrl(static::URL_PATH_DELETE) . '?download_id=' . $downloadID;
    }

    /**
     * Get allowed extensions
     *
     * @return mixed
     */
    public function getExtensions()
    {
        $mimeTypes = $this->_scopeConfig->getValue('sebwite_productdownloads/general/extension');

        return $mimeTypes;
    }
}
