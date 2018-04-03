<?php
/**
 * Created by PhpStorm.
 * User: mahdynasr
 * Date: 03/04/18
 * Time: 10:35 AM
 */
namespace MahdyNasr\Newsletter\Block;

use MahdyNasr\Newsletter\Model\NewsletterCookieFactory;
use Magento\Newsletter\Model\Subscriber;

class Subscribe extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \MahdyNasr\Newsletter\Model\NewsletterCookie
     */
    private $newsletterCookie;

    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    private $subscriber;

    /**
     * Subscribe constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param NewsletterCookieFactory $newsletterCookieFactory
     * @param Subscriber $subscriber
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        NewsletterCookieFactory $newsletterCookieFactory,
        Subscriber $subscriber,
        array $data
    )
    {
        $this->subscriber = $subscriber;
        $this->newsletterCookie = $newsletterCookieFactory->create();
        parent::__construct($context, $data);
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('mahdynewsletter/subscriber/new', ['_secure' => true]);
    }

    /**
     * check if the visitor is allowed to see the subscription form
     * return true if he is not subscribed and false otherwise
     * @return bool
     */
    public function isAllowable()
    {
        return $this->newsletterCookie->get() != Subscriber::STATUS_SUBSCRIBED;

    }
}
