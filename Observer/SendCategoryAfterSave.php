<?php

namespace EasySales\Integrari\Observer;

use EasySales\Integrari\Core\EasySales;
use Magento\Catalog\Model\Category;
use EasySales\Integrari\Core\Transformers\Category as CategoryTransformer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SendCategoryAfterSave implements ObserverInterface
{
    private $easySales;
    /**
     * @var \EasySales\Integrari\Core\Transformers\Category
     */
    private $categoryTransformer;

    public function __construct(
        EasySales $easySales,
        CategoryTransformer $categoryTransformer
    )
    {
        $this->easySales = $easySales;
        $this->categoryTransformer = $categoryTransformer;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Category $category */
        $category = $observer->getEvent()->getData('category');
        if ($category->getData('easysales_should_send') === false) {
            return;
        }

        $transformed = $this->categoryTransformer->transform($category);

        $this->easySales->execute("sendCategory", ['category' => $transformed->toArray()]);
    }
}
