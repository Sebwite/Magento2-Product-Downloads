<?php namespace Sebwite\ProductDownloads\Controller\Adminhtml\Delete;

use Magento\Framework\App\Action\Action;
use Sebwite\ProductDownloads\Model\Download;
use Sebwite\ProductDownloads\Model\DownloadFactory;

/**
 * Class:Index
 * Sebwite\ProductDownloads\Controller\Adminhtml\Delete
 *
 * @author      Sebwite
 * @package     Sebwite\ProductDownloads
 * @copyright   Copyright (c) 2015, Sebwite. All rights reserved
 */
class Index extends Action
{

    /**
     * @var Download
     */
    private $download;
    /**
     * @var
     */
    private $downloadFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param Download                            $download
     * @param DownloadFactory                     $downloadFactory
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, Download $download, DownloadFactory $downloadFactory)
    {
        $this->download = $download;
        $this->downloadFactory = $downloadFactory;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($downloadId = $this->getRequest()->getParam('download_id')) {
            $name = "";

            try {
                /** @var \Sebwite\ProductDownloads\Model\Download $download */
                $download = $this->downloadFactory->create();
                $download->load($downloadId);
                $name = $download->getName();
                $productId = $download['product_id'];
                $storeId = $download['store_id'];
                $download->delete();
                $this->messageManager->addSuccess(__('The download has been deleted.'));
                $this->_eventManager->dispatch('adminhtml_sebwite_productdownloads_download_on_delete', ['name' => $name, 'status' => 'success']);

                $resultRedirect->setPath('catalog/product/edit/*', ['id' => $productId, 'active_tab' => 'downloads', 'store' => $storeId]);

                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch('adminhtml_sebwite_productdownloads_delete_on_delete', ['name' => $name, 'status' => 'fail']);
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('catalog/product/edit/', ['id' => $productId, 'active_tab' => 'downloads', 'store' => $storeId]);

                return $resultRedirect;
            }
        }

        // display error message
        $this->messageManager->addError(__('We can\'t find a download to delete.'));

        // go to grid
        $resultRedirect->setPath('catalog/product/index');

        return $resultRedirect;
    }
}
