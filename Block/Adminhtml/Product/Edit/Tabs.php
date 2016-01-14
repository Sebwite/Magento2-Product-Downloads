<?php namespace Sebwite\ProductDownloads\Block\Adminhtml\Product\Edit;

/**
 * Class:Tabs
 * Sebwite\ProductDownloads\Block\Adminhtml\Product\Edit
 *
 * @author      Sebwite
 * @package     Sebwite\ProductDownloads
 * @copyright   Copyright (c) 2015, Sebwite. All rights reserved
 */
class Tabs extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs {

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->addTab('downloads', ['label' => __('Downloads'), 'content' => $this->_translateHtml($this->getLayout()->createBlock('Sebwite\ProductDownloads\Block\Adminhtml\Product\Edit\Tab\Download')->toHtml()), 'group_code' => self::BASIC_TAB_GROUP_CODE]);
    }
}