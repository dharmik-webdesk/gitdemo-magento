<?php
namespace MageArray\ProductQuestions\Block\Adminhtml\Answers\Edit\Tab;

class Newanswer extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_status;

    protected $_customer;

    protected $_question;

    protected $_answerby;

    protected $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \MageArray\ProductQuestions\Model\Status $status,
        \MageArray\ProductQuestions\Model\Question $question,
        \MageArray\ProductQuestions\Model\Answerby $answerby,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MageArray\ProductQuestions\Model\Customer $customer,
        array $data = []
    ) {
        $this->_status = $status;
        $this->_customer = $customer;
        $this->_question = $question;
        $this->_answerby = $answerby;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('answers_post');

        $isElementDisabled = false;

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form
            ->addFieldset(
                'base_fieldset',
                ['legend' => __('Answer Information')]
            );
        if ($model->getId()) {
            $fieldset->addField(
                'answers_id',
                'hidden',
                ['name' => 'answers_id']
            );
        }

        $fieldset->addField(
            'product_questions_id',
            'select',
            [
                'label' => __('Question'),
                'title' => __('Question'),
                'name' => 'product_questions_id',
                'required' => true,
                'options' => $this->_question->getOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );

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
                'disabled' => $isElementDisabled,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );

        $fieldset->addField(
            'answers_from',
            'select',
            [
                'label' => __('Answer From'),
                'title' => __('Answer From'),
                'name' => 'answers_from',
                'required' => true,
                'options' => $this->_answerby->getOptionArray(),
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
                'display' => 'none'
            ],
            'answers_from'
        );

        $fieldset->addField(
            'author_name',
            'text',
            [
                'label' => __('Author Name'),
                'title' => __('Author Name'),
                'name' => 'guest_name',
                'required' => true,
                'disabled' => $isElementDisabled,
                'display' => 'none'
            ],
            'answers_from'
        );

        $fieldset->addField(
            'author_email',
            'text',
            [
                'label' => __('Author Email'),
                'title' => __('Author Email'),
                'name' => 'guest_email',
                'required' => true,
                'disabled' => $isElementDisabled,
                'display' => 'none'
            ],
            'answers_from'
        );

        $fieldset->addField(
            'admin_name',
            'text',
            [
                'label' => __('Author Name'),
                'title' => __('Author Name'),
                'name' => 'admin_name',
                'required' => true,
                'disabled' => $isElementDisabled,
                'display' => 'none'
            ],
            'answers_from'
        );

        $fieldset->addField(
            'admin_email',
            'text',
            [
                'label' => __('Author Email'),
                'title' => __('Author Email'),
                'name' => 'admin_email',
                'required' => true,
                'disabled' => $isElementDisabled,
                'display' => 'none'
            ],
            'answers_from'
        );

        $fieldset->addField(
            'answers',
            'editor',
            [
                'name' => 'answers',
                'label' => __('Answers'),
                'title' => __('Answers'),
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

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                "{$htmlIdPrefix}answers_from",
                'answers_from'
            )->addFieldMap(
                "{$htmlIdPrefix}customer_id",
                'customer_id'
            )->addFieldMap(
                "{$htmlIdPrefix}author_name",
                'author_name'
            )->addFieldMap(
                "{$htmlIdPrefix}author_email",
                'author_email'
            )->addFieldMap(
                "{$htmlIdPrefix}admin_name",
                'admin_name'
            )->addFieldMap(
                "{$htmlIdPrefix}admin_email",
                'admin_email'
            )->addFieldDependence(
                'customer_id',
                'answers_from',
                'Customer'
            )->addFieldDependence(
                'admin_name',
                'answers_from',
                'Admin'
            )->addFieldDependence(
                'admin_email',
                'answers_from',
                'Admin'
            )->addFieldDependence(
                'author_name',
                'answers_from',
                'Guest'
            )->addFieldDependence(
                'author_email',
                'answers_from',
                'Guest'
            )
        );

        $form->setValues($model->getData());
        $scopeConfig = $this->_objectManager
            ->create('Magento\Framework\App\Config\ScopeConfigInterface');

        $configPath = 'productquestions/general/admin_name';
        $value = $scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $configPaths = 'productquestions/general/admin_email';
        $values = $scopeConfig->getValue(
            $configPaths,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $form->addValues(['admin_name' => $value]);
        $form->addValues(['admin_email' => $values]);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Answer Information');
    }

    public function getTabTitle()
    {
        return __('Answer Information');
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
