<?php
declare(strict_types=1);

namespace TonyB\AdminPolicy\Block\Adminhtml\Auth;

use Magento\Backend\Block\Template\Context;
use Magento\User\Model\UserFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Directory\Helper\Data as DirectoryHelper;

/**
 * Class ForgottenPassword
 */
class ForgottenPassword extends \Magento\Backend\Block\Template {
    /**
     * User model factory
     *
     * @var UserFactory
     */
    protected $userFactory;

    public function __construct(
        Context $context,
        UserFactory $userFactory,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        parent::__construct(
            $context,
            $data,
            $jsonHelper,
            $directoryHelper
        );

        $this->userFactory = $userFactory;
    }

    /**
     * @return \Magento\User\Model\User
     */
    public function getUser() {
        return $this->userFactory->create()->load($this->getUserId());
    }
}
