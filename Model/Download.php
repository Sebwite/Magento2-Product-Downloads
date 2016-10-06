<?php namespace Sebwite\ProductDownloads\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class:Download
 * Sebwite\ProductDownloads\Model
 *
 * @author      Sebwite
 * @package     Sebwite\ProductDownloads
 * @copyright   Copyright (c) 2015, Sebwite. All rights reserved
 */
class Download extends AbstractModel implements IdentityInterface
{

    /**
     * status enabled
     *
     * @var int
     */
    const STATUS_ENABLED = 1;
    /**
     * status disabled
     *
     * @var int
     */
    const STATUS_DISABLED = 0;
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sebwite_product_download';
    protected $urlModel;
    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'sebwite_product_download';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'sebwite_product_download';

    /**
     * filter model
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * @var string
     */
    private $uploadFolder = 'sebwite/productdownloads/';

    public function __construct(FilterManager $filter, Context $context, Registry $registry, AbstractResource $resource = null, AbstractDb $resourceCollection = null, array $data = [])
    {
        $this->filter = $filter;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Prepare post's statuses.
     * Available event blog_post_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Get Download url
     *
     * @param $download
     *
     * @return string
     */
    public function getUrl($download)
    {
        return 'pub/media/' . $this->uploadFolder . $download['download_url'];
    }

    /**
     * Check if author url key exists
     * return author id if author exists
     *
     * @param string $urlKey
     * @param int    $storeId
     *
     * @return int
     */
    public function checkUrlKey($urlKey, $storeId)
    {
        return $this->_getResource()->checkUrlKey($urlKey, $storeId);
    }

    /**
     * Get downloads
     *
     * @param $downloadId
     *
     * @return bool|string
     */
    public function getDownloadsForProduct($downloadId)
    {
        return $this->getResource()->getDownloadsForProduct($downloadId);
    }

    /**
     * Get downloads
     *
     * @param $downloadId
     * @param null $storeId
     * @param bool $fallbackToDefault
     * @return mixed
     */
    public function getDownloadsForProductInStore($downloadId, $storeId = null, $fallbackToDefault = true)
    {
        return $this->getResource()->getDownloadsForProductInStore($downloadId, $storeId, $fallbackToDefault);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Sebwite\ProductDownloads\Model\Resource\Download');
    }
}
