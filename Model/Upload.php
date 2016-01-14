<?php namespace Sebwite\ProductDownloads\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Uploader;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\Exception as FrameworkException;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Class:Upload
 * Sebwite\ProductDownloads\Model
 *
 * @author      Sebwite
 * @package     Sebwite\ProductDownloads
 * @copyright   Copyright (c) 2015, Sebwite. All rights reserved
 */
class Upload {

    /**
     * uploader factory
     *
     * @var \Magento\Framework\File\UploaderFactory
     */
    protected $uploaderFactory;
    protected $mimeTypes = ['pdf', 'docx', 'jpg', 'jpeg', 'png'];
    /**
     * @var string
     */
    private $uploadPath;
    /**
     * @var string
     */
    private $uploadFolder = 'sebwite/productdownloads/';

    /**
     * constructor
     *
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem      $fileSystem
     */
    public function __construct(UploaderFactory $uploaderFactory, Filesystem $fileSystem)
    {
        $this->uploaderFactory = $uploaderFactory;
        $this->uploadPath = $fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath($this->uploadFolder);
    }

    /**
     * Upload the file
     *
     * @param $download
     *
     * @return array
     * @throws \Magento\Framework\Validator\Exception
     */
    public function uploadFile($download)
    {
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $download]);
            $uploader->setAllowedExtensions($this->mimeTypes);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->setAllowCreateFolders(true);

            $result = $uploader->save($this->uploadPath);

            return $result;

        } catch (\Exception $e) {
            if( $e->getCode() != Uploader::TMP_NAME_EMPTY ) {
                throw new \Magento\Framework\Validator\Exception(__('Disallowed file type, only these file types are allowed: %s.', implode(', ', $this->mimeTypes)));
            }
        }

        return false;
    }
}