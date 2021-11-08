<?php
declare(strict_types=1);

namespace TonyB\AdminPolicy\Model;

use Magento\Framework\Validator\DataObject;
use Magento\Framework\Validator\NotEmpty;
use Magento\Framework\Validator\StringLength;

/**
 * Class UserValidationRules
 */
class UserValidationRules extends \Magento\User\Model\UserValidationRules
{
    /**
     * Minimum length of admin password
     */
    const MIN_PASSWORD_LENGTH = 8;

    /**
     * Adds validation rule for user password
     *
     * @param \Magento\Framework\Validator\DataObject $validator
     * @return \Magento\Framework\Validator\DataObject
     * @throws \Zend_Validate_Exception
     */
    public function addPasswordRules(\Magento\Framework\Validator\DataObject $validator): DataObject
    {
        $passwordNotEmpty = new NotEmpty();
        $passwordNotEmpty->setMessage(__('Password is required field.'), NotEmpty::IS_EMPTY);
        $minPassLength = self::MIN_PASSWORD_LENGTH;
        $passwordLength = new StringLength(['min' => $minPassLength, 'encoding' => 'UTF-8']);
        $passwordLength->setMessage(
            __('Your password must be at least %1 characters.', $minPassLength),
            \Zend_Validate_StringLength::TOO_SHORT
        );

        $validator->addRule(
            $passwordNotEmpty,
            'password'
        )->addRule(
            $passwordLength,
            'password'
        );

        return $validator;
    }
}
