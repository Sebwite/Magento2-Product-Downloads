<?php
namespace Sebwite\ProductDownloads\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\State;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Object;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Sebwite\ProductDownloads\Model\DownloadFactory;

/**
 * Class:Router
 * Sebwite\ProductDownloads\Controller
 *
 * @author      Sebwite
 * @package     Sebwite\ProductDownloads
 * @copyright   Copyright (c) 2015, Sebwite. All rights reserved
 */
class Router implements RouterInterface {

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Author factory
     * @var \Sebwite\ProductDownloads\Model\DownloadFactory
     */
    protected $downloadFactory;

    /**
     * Config primary
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * Url
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * Response
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * @param ActionFactory         $actionFactory
     * @param ManagerInterface      $eventManager
     * @param UrlInterface          $url
     * @param State                 $appState
     * @param DownloadFactory       $downloadFactory
     * @param StoreManagerInterface $storeManager
     * @param ResponseInterface     $response
     * @param ScopeConfigInterface  $scopeConfig
     */
    public function __construct(ActionFactory $actionFactory, ManagerInterface $eventManager, UrlInterface $url, State $appState, DownloadFactory $downloadFactory, StoreManagerInterface $storeManager, ResponseInterface $response, ScopeConfigInterface $scopeConfig)
    {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->url = $url;
        $this->appState = $appState;
        $this->downloadFactory = $downloadFactory;
        $this->storeManager = $storeManager;
        $this->response = $response;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Validate and Match News Author and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return bool
     * //TODO: maybe remove this and use the url rewrite table.
     */
    public function match(RequestInterface $request)
    {
        if( ! $this->dispatched ) {
            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;
            /** @var Object $condition */
            $condition = new Object(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch('sebwite_productdownloads_controller_router_match_before', ['router' => $this, 'condition' => $condition]);
            $urlKey = $condition->getUrlKey();
            if( $condition->getRedirectUrl() ) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);

                return $this->actionFactory->create('Magento\Framework\App\Action\Redirect', ['request' => $request]);
            }
            if( ! $condition->getContinue() ) {
                return null;
            }

            $entities = ['downloads' => ['prefix' => $this->scopeConfig->getValue('product/url_prefix', ScopeInterface::SCOPE_STORES), 'suffix' => $this->scopeConfig->getValue('sebwite_productdownloads/download/url_suffix', ScopeInterface::SCOPE_STORES), 'list_key' => $this->scopeConfig->getValue('sebwite_productdownloads/download/list_url', ScopeInterface::SCOPE_STORES), 'list_action' => 'index', 'factory' => $this->downloadFactory, 'controller' => 'product', 'action' => 'view', 'param' => 'id',]];

            foreach($entities as $entity => $settings) {
                if( $settings['list_key'] ) {
                    if( $urlKey == $settings['list_key'] ) {

                        $request->setModuleName('sebwite_downloads')->setControllerName($settings['controller'])->setActionName($settings['list_action']);
                        $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                        $this->dispatched = true;

                        return $this->actionFactory->create('Magento\Framework\App\Action\Forward', ['request' => $request]);
                    }
                }
                if( $settings['prefix'] ) {
                    $parts = explode('/', $urlKey);
                    if( $parts[0] != $settings['prefix'] || count($parts) != 2 ) {
                        continue;
                    }
                    $urlKey = $parts[1];
                }
                if( $settings['suffix'] ) {
                    $suffix = substr($urlKey, -strlen($settings['suffix']) - 1);
                    if( $suffix != '.' . $settings['suffix'] ) {
                        continue;
                    }
                    $urlKey = substr($urlKey, 0, -strlen($settings['suffix']) - 1);
                }
                /** @var \Sample\News\Model\Author $instance */
                $instance = $settings['factory']->create();

                $request->setModuleName('sebwite_productdownloads')->setControllerName('test')->setActionName('poep')->setParam('id', $urlKey);

                $request->setDispatched(true);
                $this->dispatched = true;

                return $this->actionFactory->create('Magento\Framework\App\Action\Forward', ['request' => $request]);
            }
        }

        return null;
    }
}