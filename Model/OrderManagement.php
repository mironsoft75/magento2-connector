<?php

namespace EasySales\Integrari\Model;

use EasySales\Integrari\Api\OrderManagementInterface;
use EasySales\Integrari\Core\Auth\CheckWebsiteToken;
use EasySales\Integrari\Core\Transformers\Order;
use EasySales\Integrari\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderManagement extends CheckWebsiteToken implements OrderManagementInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteria;

    /**
     * @var Order
     */
    private $_orderService;

    /**
     * CategoryManagement constructor.
     * @param Data $helperData
     * @param Request $request
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Order $orderService
     * @throws \Exception
     */
    public function __construct(
        Data $helperData,
        Request $request,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Order $orderService
    ) {
        parent::__construct($request, $helperData);

        $this->orderRepository = $orderRepository;
        $this->searchCriteria = $searchCriteriaBuilder;
        $this->_orderService = $orderService;
    }

    /**
     * @return array|mixed
     */
    public function getOrders()
    {
        $page = $this->request->getQueryValue('page', 1);
        $limit = $this->request->getQueryValue('limit', self::PER_PAGE);
        $this->searchCriteria->setPageSize(100)->setCurrentPage($page);

        $list = $this->orderRepository->getList($this->searchCriteria->create());
        $orders = [];

        foreach ($list->getItems() as $order) {
            $orders[] = $this->_orderService->transform($order)->toArray();
        }

        return [[
            'perPage' => $limit,
            'pages' => ceil($list->getTotalCount() / $limit),
            'curPage' => $page,
            'orders' => $orders,
        ]];
    }
}
