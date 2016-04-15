<?php

namespace TrackerBundle\Tests\Form\Type;

use Symfony\Component\Form\PreloadedExtension;
use TrackerBundle\Form\IssueType;
use Symfony\Component\Form\Test\TypeTestCase;
use TrackerBundle\Tests\Form\Type\Stub\EntityType;

class IssueTypeTest extends TypeTestCase
{
    /**
     * @var array
     */
    protected static $entities;

    protected function getExtensions()
    {
        $entityType = new EntityType($this->getEntities());

        return [
            new PreloadedExtension(
                [
                    $entityType->getName() => $entityType,
                ],
                []
            )
        ];
    }

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
        $type = new IssueType('edit');
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
    protected function getEntities()
    {
        if (!self::$entities) {
            self::$entities = [
                // projects
                1 => $this->getEntity('TrackerBundle\Entity\Project', 1),
                2 => $this->getEntity('TrackerBundle\Entity\Project', 2),

                // assignees
                3 => $this->getEntity('TrackerBundle\Entity\User', 3),
                4 => $this->getEntity('TrackerBundle\Entity\User', 4),

                // parent issues
                5 => $this->getEntity('TrackerBundle\Entity\Issue', 5),
                6 => $this->getEntity('TrackerBundle\Entity\Issue', 6),
            ];
        }

        return self::$entities;
    }

    /**
     * @param string $className
     * @param int $id
     * @return object
     */
    protected function getEntity($className, $id)
    {
        $entity = new $className;

        $reflectionClass = new \ReflectionClass($className);
        $method = $reflectionClass->getProperty('id');
        $method->setAccessible(true);
        $method->setValue($entity, $id);

        if ($reflectionClass->hasProperty('salt')) {
            $salt = $reflectionClass->getProperty('salt');
            $salt->setAccessible(true);
            $salt->setValue($entity, '12345');
        }

        return $entity;
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
                    'summary' => 'issue_summary',
                    'code' => 'issue_code',
                    'status' => 'STATUS_IN_PROGRESS',
                    'type' => 'TYPE_TASK',
                    'priority' => 'PRIORITY_HIGH',
                    'resolution' => null,
                    'assignee' => 4,
                    'parent_issue' => 6,
                    'description' => 'Issue description',
                ],
                'expectedData' => [
                    'summary' => 'issue_summary',
                    'code' => 'issue_code',
                    'status' => 'STATUS_IN_PROGRESS',
                    'type' => 'TYPE_TASK',
                    'priority' => 'PRIORITY_HIGH',
                    'resolution' => null,
                    'assignee' => $this->getEntity('TrackerBundle\Entity\User', 4),
                    'parent_issue' => $this->getEntity('TrackerBundle\Entity\Issue', 6),
                    'description' => 'Issue description',
                ],
            ],
        ];
    }
}
