<?php
declare(strict_types=1);

namespace TonyB\AdminPolicy\Block\Adminhtml\User\Edit\Tab;

/**
 * Class Main
 */
class Main extends \Magento\User\Block\User\Edit\Tab\Main
{
    protected $validationRules = [
        'length',
        'character',
        'sensitive'
    ];

    /**
     * Modify password input field
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param string $passwordLabel
     * @param string $confirmationLabel
     * @param bool $isRequired
     * @return void
     */
    protected function _addPasswordFields(
        \Magento\Framework\Data\Form\Element\Fieldset $fieldset,
        $passwordLabel,
        $confirmationLabel,
        $isRequired = false
    ) {
        parent::_addPasswordFields($fieldset, $passwordLabel, $confirmationLabel, $isRequired);

        $requiredFieldClass = $isRequired ? ' required-entry' : '';
        $passwordFieldClasses = [];
        foreach ($this->validationRules as $rule) {
            $passwordFieldClasses[] = 'validate-admin-password-' . $rule;
        }

        $elements = $fieldset->getElements();

        foreach ($elements as $item) {
            if ($item->getName() == 'password') {
                $item->setClass('input-text ' . join(' ', $passwordFieldClasses) . $requiredFieldClass);
            }
        }
    }
}
