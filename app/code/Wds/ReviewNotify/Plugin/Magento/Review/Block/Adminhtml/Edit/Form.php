<?php
namespace Wds\ReviewNotify\Plugin\Magento\Review\Block\Adminhtml\Edit;

class Form extends \Magento\Review\Block\Adminhtml\Edit\Form
{
        public function __construct(
            \Magento\Directory\Model\CountryFactory $countryFactory,
            \Magento\Directory\Model\RegionFactory $regionFactory,
            \Wds\ReviewNotify\Model\OptionsExperts $OptionsExperts
        )
        {
            $this->_countryFactory = $countryFactory;
            $this->_regionFactory=$regionFactory;
            $this->_optionsExperts = $OptionsExperts;
        }


    public function beforeSetForm(\Magento\Review\Block\Adminhtml\Edit\Form $object, $form) {



        //$countries = $this->_countryFactory->toOptionArray(false, 'US');
        $regionCollection = $this->_regionFactory->create()->getCollection()->addCountryFilter('us');
        $regions = $regionCollection->toOptionArray();


        $review = $object->_coreRegistry->registry('review_data');

        $fieldset = $form->addFieldset(
            'review_details_extra',
            ['legend' => __('Review Details Extra Data'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'email',
            'text',
            ['label' => __('E-mail'), 'required' => true, 'name' => 'email']
        );
        $fieldset->addField(
            'company',
            'text',
            ['label' => __('Company'),  'name' => 'company']
        );

        $fieldset->addField(
            'city',
            'text',
            ['label' => __('City'), 'name' => 'city']
        );

        $fieldset->addField(
            'state',
            'select',
            ['label' => __('State'),  'name' => 'state','values' => $regions]
        );


        $fieldset = $form->addFieldset(
            'review_details_extra_expert',
            ['legend' => __('Expert Reponse'), 'class' => 'fieldset-wide']
        );


        $fieldset->addField(
            'experts',
            'select',
            ['label' => __('Expert'),  'name' => 'experts','values' => $this->_optionsExperts->getOptionArray()]
        );

        $fieldset->addField(
            'message',
            'textarea',
            ['label' => __('Message'),  'name' => 'message']
        );
        


        $form->setValues($review->getData());
        return [$form];
    }
}