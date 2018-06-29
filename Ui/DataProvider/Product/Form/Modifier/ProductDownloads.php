<?php namespace Sebwite\ProductDownloads\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Fieldset;
use Sebwite\ProductDownloads\Model\Download;

class ProductDownloads extends AbstractModifier
{
    protected $_amountDownloads = 10;

    /** @var \Sebwite\ProductDownloads\Model\Download */
    private $download;

    /** @var \Magento\Framework\Registry */
    private $registry;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    public function __construct(
        Download $download,
        Registry $registry,
        StoreManagerInterface $storeManager
    ) {
        $this->download     = $download;
        $this->registry     = $registry;
        $this->storeManager = $storeManager;
    }

    public function modifyMeta(array $meta)
    {
        $fields = $this->getDownloadFields();

        $meta[ 'test_fieldset_name' ] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Product Downloads'),
                        'sortOrder'     => 50,
                        'collapsible'   => true,
                        'componentType' => Fieldset::NAME,
                    ]
                ]
            ],
            'children'  => $fields
        ];

        return $meta;
    }

    protected function getDownloadFields()
    {
        $fields   = [];
        $_product = $this->registry->registry('current_product');

        $downloads = $this->download->getDownloadsForProduct($_product->getId());

        for ($i = 0; $i < $this->_amountDownloads; $i++) {

            if (isset($downloads[ $i ])) {
                $fields[ 'data.product.remove_download[' . $i . ']' ] = [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement'   => 'checkbox',
                                'componentType' => 'field',
                                'description'   => $downloads[ $i ][ 'download_file' ],
                                'dataScope'     => 'data.product.remove_download][' . $downloads[ $i ][ 'download_id' ],
                                'checked'       => true,
                                'value'         => true,
                                'visible'       => 1,
                                'required'      => 0,
                                'label'         => __('File %1', $i +1),
                                'comment'       => '<a href="' . $this->storeManager->getStore()->getBaseUrl() . $this->download->getUrl($downloads[ $i ]) . '" target="_blank">' . $downloads[ $i ][ 'download_file' ] . '</a>'
                            ]
                        ]
                    ]
                ];
            } else {
                $fields[ 'sebwite.downloads[' . $i . ']' ] = [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement'   => 'file',
                                'dataScope'     => 'sebwite.downloads[]',
                                'componentType' => 'field',
                                'visible'       => 1,
                                'required'      => 0,
                                'label'         => __('File %1', $i + 1)
                            ]
                        ]
                    ]
                ];
            }
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}