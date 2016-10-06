<?php namespace Sebwite\ProductDownloads\Model\Resource;

use Magento\Catalog\Model\Product;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class:Download
 * Sebwite\ProductDownloads\Model\Resource
 *
 * @author      Sebwite
 * @package     Sebwite\ProductDownloads
 * @copyright   Copyright (c) 2015, Sebwite. All rights reserved
 */
class Download extends AbstractDb
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime       $date
     * @param \Magento\Framework\Stdlib\DateTime                $dateTime
     * @param string|null                                       $resourcePrefix
     */
    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Framework\Stdlib\DateTime $dateTime, $resourcePrefix = null)
    {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
    }

    /**
     * Load an object using 'product_id' field if there's no field specified and value is not numeric
     *
     * @param AbstractModel|\Sebwite\ProductDownloads\Model\Download $object
     * @param mixed                                                  $value
     * @param string                                                 $field
     *
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        if (! is_numeric($value) && is_null($field)) {
            $field = 'product_id';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Retrieve all Downloads for product
     *
     * @param string $id
     *
     * @return string|bool
     */
    public function getDownloadsForProduct($id)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from($this->getMainTable())->where('product_id = :product_id');
        $binds = ['product_id' => (int) $id];

        return $adapter->fetchAll($select, $binds);
    }

    /**
     * Retrieve all Downloads for product - within the desired store view.
     *
     * @param $id
     * @param null $storeId
     * @param bool $fallbackToDefault
     * @return array
     */
    public function getDownloadsForProductInStore($id, $storeId = null, $fallbackToDefault = true)
    {
        $adapter = $this->getConnection();

        if (is_null($storeId)) {
            $storeId = 0;
        }

        $storeWhereStatement = (int) $storeId;

        if ($fallbackToDefault) {
            $storeWhereStatement = 'IF((SELECT COUNT(*) AS count FROM ' . $this->getMainTable() . ' WHERE store_id = '. (int) $storeId .') > 0, ' . (int) $storeId . ', 0)';
        }

        $select = $adapter->select()->from($this->getMainTable())->where('product_id = :product_id AND store_id = ' . $storeWhereStatement);
        $binds = [
            'product_id' => (int) $id
        ];

        return $adapter->fetchAll($select, $binds);
    }

    /**
     * Check if author url_key exist
     * return author id if author exists
     *
     * @param string $urlKey
     * @param int    $storeId
     *
     * @return int
     */
    public function checkUrlKey($urlKey, $storeId)
    {
        $stores = [Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByUrlKeySelect($urlKey, $stores, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('download_id')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sebwite_product_downloads', 'download_id');
    }

    /**
     * Process post data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        return parent::_beforeSave($object);
    }

    /**
     * Process download data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    protected function _beforeDelete(AbstractModel $object)
    {
        $condition = ['download_id = ?' => (int) $object->getId()];
        $this->getConnection()->delete($this->getTable('sebwite_product_downloads'), $condition);

        return parent::_beforeDelete($object);
    }
}
