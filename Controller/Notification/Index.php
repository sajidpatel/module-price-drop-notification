<?php
declare(strict_types=1);

namespace SajidPatel\PriceDropNotification\Controller\Notification;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\RedirectFactory;

class Index implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * Display customer's price drop notifications
     *
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('customer/account/login');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Price Drop Notifications'));

        return $resultPage;
    }
}
