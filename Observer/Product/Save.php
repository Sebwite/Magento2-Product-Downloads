<?php namespace Sebwite\ProductDownloads\Observer\Product;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Registry;
use Sebwite\ProductDownloads\Model\Upload;
use Sebwite\ProductDownloads\Model\DownloadFactory;

class Save implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var Sebwite\ProductDownloads\Model\Upload
     */
    private $upload;

    /**
     * @var Magento\Backend\App\Action\Context
     */
    private $context;

    /** @var \Sebwite\ProductDownloads\Model\DownloadFactory */
    private $downloadFactory;

    public function __construct(
        Registry $coreRegistry,
        Upload $upload,
        Context $context,
        DownloadFactory $downloadFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->upload       = $upload;
        $this->context      = $context;
        $this->downloadFactory = $downloadFactory;
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
        $post = $observer->getDataObject();

        // Get current product
        $product = $this->coreRegistry->registry('product');

        // Delete old downloads
        $this->deleteOldDownloads($post, $product);

        if ($downloads != '-1') {
            $this->addDownloads($downloads, $product);
        }

        return $this;
    }

    private function addDownloads($downloads, $product)
    {
        $productId = $product->getId();

        // Loop through uploaded downlaods
        foreach ($downloads as $download) {

            if ($download[ 'tmp_name' ] === "") {
                continue;
            }

            // Upload file
            $uploadedDownload = $this->upload->uploadFile($download);

            if ($uploadedDownload) {
                $objectManager = $this->context->getObjectManager();
                // Store date in database
                $download = $objectManager->create('Sebwite\ProductDownloads\Model\Download');

                $download->setDownloadUrl($uploadedDownload[ 'file' ]);
                $download->setDownloadFile($uploadedDownload[ 'name' ]);
                $download->setDownloadType($uploadedDownload[ 'type' ]);
                $download->setProductId($productId);
                $download->save();
            }
        }
    }

    private function deleteOldDownloads($post, $product)
    {
        if (isset($post['remove_download'])) {
            foreach ($post['remove_download'] as $deleteId => $keep) {

                if($keep === "0") {
                    /** @var \Sebwite\ProductDownloads\Model\Download $download */
                    $download = $this->downloadFactory->create();
                    $download->load($deleteId);
                    $download->delete();
                }
            }
        }
    }
}