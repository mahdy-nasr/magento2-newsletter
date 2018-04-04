<?php
/**
 * Created by PhpStorm.
 * User: mahdynasr
 * Date: 03/04/18
 * Time: 9:20 AM
 */
namespace MahdyNasr\Newsletter\Controller\Subscriber;

use Magento\Framework\App\Action\Context;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\Controller\ResultFactory;
use MahdyNasr\Newsletter\Model\NewsletterCookie;
use MahdyNasr\Newsletter\Model\NewsletterCookieFactory;


class NewAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var NewsletterCookie
     */
    protected $newsletterCookie;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * NewAction constructor.
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param ResultFactory $resultFactory
     * @param NewsletterCookieFactory $newsletterCookieFactory
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        ResultFactory $resultFactory,
        NewsletterCookieFactory $newsletterCookieFactory
    )
    {
        parent::__construct($context);
        $this->subscriberFactory = $subscriberFactory;
        $this->resultFactory = $resultFactory;
        $this->newsletterCookie = $newsletterCookieFactory->create();
    }

    /**
     * Validates the format of the email address
     *
     * @param string $email
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function validateEmailFormat($email)
    {
        if (!\Zend_Validate::is($email, \Magento\Framework\Validator\EmailAddress::class)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a valid email address.'));
        }
    }

    /**
     * @return \Magento\Framework\Controller\Result
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                ->setUrl($this->_redirect->getRedirectUrl());
        }

        $email = (string)$this->getRequest()->getPost('email');
        $first_name = $this->getRequest()->getPost('name');

        try {
            $this->validateEmailFormat($email);

            // check if subscriber is already subscribed
            $subscriber = $this->subscriberFactory->create()->loadByEmail($email);

            if ($subscriber->getId()
                && $subscriber->getSubscriberStatus() == Subscriber::STATUS_SUBSCRIBED
            ) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('This email address is already subscribed.')
                );
            }


            $status = $this->subscriberFactory->create()
                ->setFirstName($first_name)
                ->subscribe($email);

            // set the subscription status on cookies
            $this->newsletterCookie->set($status);

            if ($status == Subscriber::STATUS_NOT_ACTIVE) {
                $this->messageManager->addSuccess(__('The confirmation request has been sent.'));
            } else {
                $this->messageManager->addSuccess(__('Thank you for your subscription.'));
            }

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addException(
                $e,
                __('There was a problem with the subscription: %1', $e->getMessage())
            );
        } catch (\Exception $e) {

            // set cookie in case the problem was from mail sender or something like that.
            if ($this->subscriberFactory->create()->loadByEmail($email)->isSubscribed()) {
                $this->newsletterCookie->set(Subscriber::STATUS_SUBSCRIBED);
            }

            $this->messageManager->addException($e, __('Something went wrong with the subscription.'));
        }


        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
            ->setUrl($this->_redirect->getRedirectUrl());
    }


}
