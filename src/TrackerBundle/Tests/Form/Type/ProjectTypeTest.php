<?php

namespace TrackerBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use TrackerBundle\Form\ProjectType;

class ProjectTypeTest extends TypeTestCase
{
    /**
     * @dataProvider submitDataProvider
     *
     * @param array $options
     * @param array $defaultData
     * @param array $viewData
     * @param array $submittedData
     * @param array $expectedData
     */
    public function testSubmit(
        array $options,
        array $defaultData,
        array $viewData,
        array $submittedData,
        array $expectedData
    ) {
        $type = new ProjectType();
        $form = $this->factory->create($type, $defaultData, $options);

        $formConfig = $form->getConfig();
        $this->assertNull($formConfig->getOption('data_class'));

        $this->assertEquals($defaultData, $form->getData());
        $this->assertEquals($viewData, $form->getViewData());

        $form->submit($submittedData);
        $this->assertTrue($form->isValid());
        $this->assertEquals($expectedData, $form->getData());

        $this->assertTrue($form->isSynchronized());
    }

    /**
     * @return array
     */
    public function submitDataProvider()
    {
        return [
            'default' => [
                'options' => [
                    'data_class' => null,
                ],
                'defaultData' => [],
                'viewData' => [],
                'submittedData' => [
                    'label' => 'project_label',
                    'code' => 'project_code',
                    'summary' => 'project_summary',
                ],
                'expectedData' => [
                    'label' => 'project_label',
                    'code' => 'project_code',
                    'summary' => 'project_summary',
                ],
            ],
        ];
    }
}
