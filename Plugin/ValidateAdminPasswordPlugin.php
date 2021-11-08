<?php
declare(strict_types=1);

namespace TonyB\AdminPolicy\Plugin;

use Magento\Framework\Validator\Regex;

/**
 * Class ValidateAdminPasswordPlugin
 */
class ValidateAdminPasswordPlugin
{
    const SUBSTRING_LENGTH_LIMIT = 3;

    /**
     * @param \Magento\User\Model\User $subject
     * @param callable $proceed
     * @return array|bool
     * @throws \Zend_Validate_Exception
     */
    public function aroundValidate(\Magento\User\Model\User $subject, callable $proceed)
    {
        if ($subject->getPassword() && !$subject->getForceNewPassword()) {
            $validators = [];

            $lowercaseValidate = new Regex('/[a-z]/u');
            $lowercaseValidate->setMessage(
                __('Your password must include lowercase characters.'),
                \Zend_Validate_Regex::NOT_MATCH
            );
            $validators[] = $lowercaseValidate;

            $uppercaseValidate = new Regex('/[A-Z]/u');
            $uppercaseValidate->setMessage(
                __('Your password must include uppercase characters.'),
                \Zend_Validate_Regex::NOT_MATCH
            );
            $validators[] = $uppercaseValidate;

            $numericValidate = new Regex('/\d/u');
            $numericValidate->setMessage(
                __('Your password must include numeric characters.'),
                \Zend_Validate_Regex::NOT_MATCH
            );
            $validators[] = $numericValidate;

            $nonAlphabeticValidate = new Regex('/\W/iu');
            $nonAlphabeticValidate->setMessage(
                __('Your password must include non-alphabetic characters.'),
                \Zend_Validate_Regex::NOT_MATCH
            );
            $validators[] = $nonAlphabeticValidate;

            $validConditionCount = 0;
            foreach ($validators as $validator) {
                if ($validator->isValid($subject->getPassword())) {
                    $validConditionCount++;
                }
            }
            if ($validConditionCount < 3) {
                return ['Passwords must contain characters from three of the following four categories: ' .
                    'uppercase characters, lowercase characters, numeric characters, non-alphabetic characters: '.
                    '! @ # & ( ) – [ { } ] : ; \', ? / * ` ~ $ ^ + = < > “'
                ];
            }

            if (!$this->checkSensitiveDataInPassword($subject)) {
                return [
                    'Passwords cannot contain ' .
                    'the user\'s account name or parts of the user\'s full name that exceed two consecutive characters.'
                ];
            }
        }

        return $proceed();
    }

    /**
     * @param \Magento\User\Model\User $user
     * @return bool
     */
    public function checkSensitiveDataInPassword(\Magento\User\Model\User $user): bool
    {
        $password = strtolower($user->getPassword());

        $tokens = [];

        $tokens = array_merge(
            $tokens,
            $this->splitSubStringByLength(strtolower($user->getFirstName()), self::SUBSTRING_LENGTH_LIMIT)
        );

        $tokens = array_merge(
            $tokens,
            $this->splitSubStringByLength(strtolower($user->getLastName()), self::SUBSTRING_LENGTH_LIMIT)
        );

        $tokens[] = strtolower($user->getUserName());

        foreach ($tokens as $token) {
            if (strpos($password, $token) !== false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all possible substrings specified by length.
     * @param $string
     * @param $length
     * @return array
     */
    private function splitSubStringByLength($string, $length): array
    {
        $result = [];
        $stringLength = strlen($string);
        $index = 0;
        while ($index < $stringLength - $length + 1) {
            $result[] = substr($string, $index, $length);
            $index++;
        }

        return $result;
    }
}
