<?php

namespace Ingrid\Checkout\Model\Config\Source;

class ProductAttributes implements \Magento\Framework\Data\OptionSourceInterface
{

    protected $collectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray(){

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('*');
        $attributesArray = array();
        $attributesArray = array(
            array(
            'label' => __('None'),
            'value' => ''
            )
        );

        foreach ($collection->getItems() as $attribute) {
            if($attribute->getIsVisibleOnFront()) {
                $attributesArray[] = array('value'=> $attribute->getAttributeCode(),'label'=> $attribute->getFrontendLabel());
            }
        }
        return $attributesArray;
    }
}
