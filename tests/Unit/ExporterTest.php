<?php

use Encore\Admin\Grid\Exporter;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class ExporterTest extends PHPUnitTestCase
{
    protected function tearDown(): void
    {
        Exporter::setQueryName('_export_');

        parent::tearDown();
    }

    public function testFormatsAllRecordsScope()
    {
        $this->assertSame(
            ['_export_' => 'all'],
            Exporter::formatExportQuery(Exporter::SCOPE_ALL)
        );
    }

    public function testFormatsCurrentPageScope()
    {
        $this->assertSame(
            ['_export_' => 'page:3'],
            Exporter::formatExportQuery(Exporter::SCOPE_CURRENT_PAGE, 3)
        );
    }

    public function testFormatsSelectedRowsScope()
    {
        $this->assertSame(
            ['_export_' => 'selected:2,5,8'],
            Exporter::formatExportQuery(Exporter::SCOPE_SELECTED_ROWS, '2,5,8')
        );
    }

    public function testUsesConfiguredQueryName()
    {
        Exporter::setQueryName('download');

        $this->assertSame(
            ['download' => 'all'],
            Exporter::formatExportQuery(Exporter::SCOPE_ALL)
        );
    }
}
