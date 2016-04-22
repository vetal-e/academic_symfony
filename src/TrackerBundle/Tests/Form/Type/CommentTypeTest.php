<?php

namespace TrackerBundle\Tests\Form\Type;

use TrackerBundle\Form\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentTypeTest extends TypeTestCase
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
        $type = new CommentType('edit');
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
                    'body' => 'comment_body',
                ],
                'expectedData' => [
                    'body' => 'comment_body',
                ],
            ],
        ];
    }
}
