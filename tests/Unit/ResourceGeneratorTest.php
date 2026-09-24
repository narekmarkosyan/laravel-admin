<?php

use Encore\Admin\Console\ResourceGenerator;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class ResourceGeneratorTest extends PHPUnitTestCase
{
    public function testGeneratesFieldsUsingNativeSchemaMetadata()
    {
        $columns = [
            ['name' => 'id', 'type_name' => 'integer', 'type' => 'integer', 'default' => null],
            ['name' => 'title', 'type_name' => 'varchar', 'type' => 'varchar', 'default' => null],
            ['name' => 'enabled', 'type_name' => 'tinyint', 'type' => 'tinyint(1)', 'default' => null],
            ['name' => 'description', 'type_name' => 'text', 'type' => 'text', 'default' => null],
        ];

        $schema = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getColumns'])
            ->getMock();
        $schema->expects($this->exactly(3))
            ->method('getColumns')
            ->with('resource_generator_items')
            ->willReturn($columns);

        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSchemaBuilder'])
            ->getMock();
        $connection->method('getSchemaBuilder')->willReturn($schema);

        $model = $this->getMockBuilder(Model::class)
            ->onlyMethods(['getConnection', 'getTable'])
            ->getMock();
        $model->method('getConnection')->willReturn($connection);
        $model->method('getTable')->willReturn('resource_generator_items');

        $generator = new ResourceGenerator($model);

        $form = $generator->generateForm();

        $this->assertStringContainsString("\$form->text('title'", $form);
        $this->assertStringContainsString("\$form->switch('enabled'", $form);
        $this->assertStringContainsString("\$form->textarea('description'", $form);
        $this->assertStringNotContainsString("\$form->text('id'", $form);
        $this->assertStringContainsString("\$show->field('title'", $generator->generateShow());
        $this->assertStringContainsString("\$grid->column('title'", $generator->generateGrid());
    }
}
