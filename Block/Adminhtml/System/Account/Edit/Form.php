<?php
declare(strict_types=1);

namespace TonyB\AdminPolicy\Block\Adminhtml\System\Account\Edit;

/**
 * Class Form
 */
class Form extends \Magento\Backend\Block\System\Account\Edit\Form
{
    protected $validationRules = [
        'length',
        'character',
        'sensitive'
    ];

    /**
     * @inheritdoc
     */
    public function _prepareForm()
    {
        $result = parent::_prepareForm();

        $passwordFieldClasses = [];
        foreach ($this->validationRules as $rule) {
            $passwordFieldClasses[] = 'validate-admin-password-' . $rule;
        }

        $elements = $this->getForm()->getElement('base_fieldset')->getElements();

        foreach ($elements as $item) {
            if ($item->getName() == 'password') {
                $item->setClass(join(' ', $passwordFieldClasses));
            }
        }

        return $result;
    }
}
