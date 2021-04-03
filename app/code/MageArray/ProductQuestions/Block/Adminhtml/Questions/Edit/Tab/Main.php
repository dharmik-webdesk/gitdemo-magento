<?php
namespace MageArray\ProductQuestions\Block\Adminhtml\Questions\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_systemStore;

    protected $_status;

    protected $_customer;

    protected $_questionby;

    protected $_visibility;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \MageArray\ProductQuestions\Model\Status $status,
        \MageArray\ProductQuestions\Model\Customer $customer,
        \MageArray\ProductQuestions\Model\Questionby $questionby,
        \MageArray\ProductQuestions\Model\Visibility $visibility,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        $this->_customer = $customer;
        $this->_questionby = $questionby;
        $this->_visibility = $visibility;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('questions_post');

        $isElementDisabled = false;

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Question Information')]
        );
        if ($model->getId()) {
            $fieldset->addField(
                'product_questions_id',
                'hidden',
                ['name' => 'product_questions_id']
            );
        }

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::SHORT
        );

        $fieldset->addField(
            'created_at',
            'date',
            [
                'name' => 'created_at',
                'label' => __('Created At'),
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'required' => true,
                'disabled' => true,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );

        $fieldset->addField(
            'asked_from',
            'select',
            [
                'label' => __('Asked By'),
                'title' => __('Asked By'),
                'name' => 'asked_from',
                'required' => true,
                'options' => $this->_questionby->getOptionArray(),
                'disabled' => $isElementDisabled
            ],
            'to'
        );

        $fieldset->addField(
            'customer_id',
            'select',
            [
                'label' => __('Author'),
                'title' => __('Author'),
                'name' => 'customer_id',
                'required' => true,
                'options' => $this->_customer->getOptionArray(),
                'disabled' => $isElementDisabled,
                'display' => 'none'
            ],
            'asked_from'
        );

        $fieldset->addField(
            'author_name',
            'text',
            [
                'label' => __('Author Name'),
                'title' => __('Author Name'),
                'name' => 'author_name',
                'required' => true,
                'disabled' => $isElementDisabled,
                'display' => 'none'
            ],
            'asked_from'
        );

        $fieldset->addField(
            'author_email',
            'text',
            [
                'label' => __('Author Email'),
                'title' => __('Author Email'),
                'name' => 'author_email',
                'required' => true,
                'disabled' => $isElementDisabled,
                'display' => 'none'
            ],
            'asked_from'
        );

        $fieldset->addField(
            'questions',
            'editor',
            [
                'name' => 'questions',
                'label' => __('Questions'),
                'title' => __('Questions'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => $this->_status->getOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'visibility',
            'select',
            [
                'label' => __('Visibility'),
                'title' => __('Visibility'),
                'name' => 'visibility',
                'required' => true,
                'options' => $this->_visibility->getOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore
                        ->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                    'name' => 'stores[]',
                    'value' => $this->_storeManager->getStore(true)->getId()
                ]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                "{$htmlIdPrefix}asked_from",
                'asked_from'
            )->addFieldMap(
                "{$htmlIdPrefix}customer_id",
                'customer_id'
            )->addFieldMap(
                "{$htmlIdPrefix}author_name",
                'author_name'
            )->addFieldMap(
                "{$htmlIdPrefix}author_email",
                'author_email'
            )->addFieldDependence(
                'customer_id',
                'asked_from',
                'Customer'
            )->addFieldDependence(
                'author_name',
                'asked_from',
                'Guest'
            )->addFieldDependence(
                'author_email',
                'asked_from',
                'Guest'
            )
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Question Information');
    }

    public function getTabTitle()
    {
        return __('Question Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
    
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
