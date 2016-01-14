<?php namespace Sebwite\ProductDownloads\Model\Adminhtml\Download;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Registry;
use Sebwite\ProductDownloads\Model\Resource\Download;
use Sebwite\ProductDownloads\Model\Upload;

/**
 * Class:Observer
 * Sebwite\ProductDownloads\Model\Adminhtml\Download
 *
 * @author      Sebwite
 * @package     Sebwite\ProductDownloads
 * @copyright   Copyright (c) 2015, Sebwite. All rights reserved
 */
class Observer implements \Magento\Framework\Event\ObserverInterface {

    /**
     * @var Upload
     */
    private $upload;
    /**
     * @var Download
     */
    private $download;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;


    public function __construct(Registry $coreRegistry, Upload $upload, \Sebwite\ProductDownloads\Model\Download $download, \Magento\Framework\ObjectManagerInterface $objectManager, Context $context)
    {
        $this->coreRegistry = $coreRegistry;
        $this->upload = $upload;
        $this->download = $download;
        $this->objectManager = $objectManager;
        $this->context = $context;
    }

    /**
     * save product data
     *
     * @param $observer
     *
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $downloads = $this->context->getRequest()->getFiles('downloads', -1);

        if( $downloads != '-1' ) {

            // Get current product
            $product = $this->coreRegistry->registry('product');
            $productId = $product->getId();

            // Loop through uploaded downlaods
            foreach($downloads as $download) {

                // Upload file
                $uploadedDownload = $this->upload->uploadFile($download);

                if( $uploadedDownload ) {

                    // Store date in database
                    $download = $this->objectManager->create('Sebwite\ProductDownloads\Model\Download');

                    $download->setDownloadUrl($uploadedDownload['file']);
                    $download->setDownloadFile($uploadedDownload['name']);
                    $download->setDownloadType($uploadedDownload['type']);
                    $download->setProductId($productId);
                    $download->save();
                }
            }
        }

        return $this;
    }
}
