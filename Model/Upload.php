<?php namespace Sebwite\ProductDownloads\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
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
class Upload
{

    /**
     * uploader factory
     *
     * @var \Magento\Framework\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var string
     */
    private $uploadPath;

    /**
     * @var string
     */
    private $uploadFolder = 'sebwite/productdownloads/';

    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * constructor
     *
     * @param UploaderFactory      $uploaderFactory
     * @param Filesystem           $fileSystem
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(UploaderFactory $uploaderFactory, Filesystem $fileSystem, ScopeConfigInterface $scopeConfig)
    {
        $this->uploaderFactory = $uploaderFactory;
        $this->uploadPath = $fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath($this->uploadFolder);
        $this->_scopeConfig = $scopeConfig;
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
            $uploader->setAllowedExtensions($this->getMimeTimes());
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->setAllowCreateFolders(true);

            $result = $uploader->save($this->uploadPath);

            return $result;
        } catch (\Exception $e) {
            if ($e->getCode() != Uploader::TMP_NAME_EMPTY) {
                throw new \Magento\Framework\Validator\Exception(__('Disallowed file type, only these file types are allowed: %s.', implode(', ', $this->getMimeTimes())));
            }
        }

        return false;
    }

    /**
     * @return array
     */
    private function getMimeTimes()
    {
        $mimeTypes = $this->_scopeConfig->getValue('sebwite_productdownloads/general/extension');

        $cleanMimeTypes = [];

        foreach (explode(',', $mimeTypes) as $mimeType) {
            array_push($cleanMimeTypes, strtolower(trim($mimeType)));
        }

        return $cleanMimeTypes;
    }
}
