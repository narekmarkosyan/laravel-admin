<?php

use Encore\Admin\AdminServiceProvider;
use Encore\Admin\Exception\Handler;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Orchestra\Testbench\TestCase;

class LaravelCompatibilityTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [AdminServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return ['Admin' => Admin::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');
        $app['config']->set('admin', require __DIR__.'/../../config/admin.php');
        $app['config']->set('admin.auth.controller', \Encore\Admin\Controllers\AuthController::class);
        $app['config']->set('admin.bootstrap', __DIR__.'/../../src/Console/stubs/bootstrap.stub');
    }

    public function testProviderRegistersMiddlewareAndAdminRoutes()
    {
        Admin::routes();

        $route = Route::getRoutes()->getByName('admin.auth.users.index');

        $this->assertNotNull($route);
        $this->assertSame('admin/auth/users', $route->uri());
        $this->assertContains('admin', $route->gatherMiddleware());
        $this->assertSame(
            \Encore\Admin\Middleware\Authenticate::class,
            $this->app['router']->getMiddleware()['admin.auth']
        );
    }

    public function testProviderSuppliesDefaultAdminUploadDisk()
    {
        $disk = config('filesystems.disks.admin');

        $this->assertSame('local', $disk['driver']);
        $this->assertSame(public_path('uploads'), $disk['root']);
        $this->assertSame('public', $disk['visibility']);
        $this->assertSame(rtrim(config('app.url'), '/').'/uploads', $disk['url']);
        $this->assertSame($disk['url'].'/images/example.jpg', Storage::disk('admin')->url('images/example.jpg'));
    }

    public function testLoginPageRenders()
    {
        Admin::routes();

        $this->get('/admin/auth/login')
            ->assertOk()
            ->assertSee('name="username"', false);
    }

    public function testToastrRendersArraySessionPayload()
    {
        admin_toastr('Saved', 'success', ['timeOut' => 3000]);

        $html = view('admin::partials.toastr')->render();

        $this->assertStringContainsString("toastr.success('Saved'", $html);
        $this->assertStringContainsString('"timeOut":3000', $html);
        $this->assertFalse(Session::has('toastr'));
    }

    public function testToastrRendersLegacyMessageBagPayload()
    {
        Session::flash('toastr', new MessageBag([
            'type' => 'warning',
            'message' => 'Legacy message',
            'options' => [],
        ]));

        $html = view('admin::partials.toastr')->render();

        $this->assertStringContainsString("toastr.warning('Legacy message'", $html);
    }

    public function testToastrRendersSerializedMessageBagArray()
    {
        Session::flash('toastr', [
            'type' => ['info'],
            'message' => ['Restored from session'],
            'options' => [],
        ]);

        $html = view('admin::partials.toastr')->render();

        $this->assertStringContainsString("toastr.info('Restored from session'", $html);
    }

    public function testAlertsRenderArraySessionPayloads()
    {
        foreach (['error' => 'admin_error', 'success' => 'admin_success', 'info' => 'admin_info', 'warning' => 'admin_warning'] as $type => $helper) {
            $helper('Alert title', 'Alert message');

            $html = view('admin::partials.alerts')->render();

            $this->assertStringContainsString('Alert title', $html);
            $this->assertStringContainsString('Alert message', $html);

            Session::forget($type);
        }
    }

    public function testAlertsRenderLegacyObjectAndSerializedArrayPayloads()
    {
        Session::flash('error', new MessageBag(['title' => 'Legacy title', 'message' => 'Legacy message']));
        Session::flash('success', ['title' => ['Serialized title'], 'message' => ['Serialized message']]);

        $html = view('admin::partials.alerts')->render();

        $this->assertStringContainsString('Legacy title', $html);
        $this->assertStringContainsString('Legacy message', $html);
        $this->assertStringContainsString('Serialized title', $html);
        $this->assertStringContainsString('Serialized message', $html);
    }

    public function testAlertsRenderValidationErrorBag()
    {
        Session::flash('errors', (new ViewErrorBag())->put('error', new MessageBag(['field' => 'Invalid field'])));

        $html = view('admin::partials.alerts')->render();

        $this->assertStringContainsString('Invalid field', $html);
    }

    public function testExceptionAlertUsesArraySessionPayload()
    {
        Handler::error('Failure', 'Exception details');

        $this->assertSame(['title' => 'Failure', 'message' => 'Exception details'], Session::get('error'));
        $this->assertStringContainsString('Exception details', view('admin::partials.alerts')->render());
    }
}
