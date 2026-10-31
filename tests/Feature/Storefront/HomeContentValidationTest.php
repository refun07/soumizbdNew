<?php

namespace Tests\Feature\Storefront;

use App\Http\Controllers\Backend\Admin\WebsiteSetup\BotanicalHomeController;
use App\Services\BotanicalHomeContent;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class HomeContentValidationTest extends TestCase
{
    private function payload(): array
    {
        $content = [];
        foreach (BotanicalHomeContent::schema() as $section => $definition) {
            foreach ($definition['fields'] as $field => $definition) {
                $content[$section][$field] = $definition['default'];
            }
        }
        return ['content' => $content];
    }

    public function test_script_links_are_rejected_before_any_settings_are_saved(): void
    {
        $payload = $this->payload();
        $payload['content']['promo_left']['link'] = 'javascript:alert(1)';
        try {
            (new BotanicalHomeController)->update(Request::create('/admin/homepage/content', 'POST', $payload));
            $this->fail('Unsafe link was accepted');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('content.promo_left.link', $exception->errors());
        }
    }

    public function test_product_limits_are_bounded_before_querying_the_catalog(): void
    {
        $payload = $this->payload();
        $payload['content']['best']['limit'] = 1000;
        $payload['content']['arrivals']['limit'] = 0;
        try {
            (new BotanicalHomeController)->update(Request::create('/admin/homepage/content', 'POST', $payload));
            $this->fail('Unbounded product limits were accepted');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('content.best.limit', $exception->errors());
            $this->assertArrayHasKey('content.arrivals.limit', $exception->errors());
        }
    }

    public function test_editor_requires_authentication(): void
    {
        $this->get('/admin/homepage/content')->assertRedirect('/login');
    }
    public function test_missing_upload_returns_json_error_instead_of_success_or_redirect(): void
    {
        $response = (new \App\Http\Controllers\Backend\Admin\FileManagerController)->upload(
            Request::create('/file-manager/upload', 'POST')
        );
        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('media_file', $response->getData(true)['errors']);
    }

    public function test_sale_deadline_uses_dhaka_timezone_and_expires_at_the_boundary(): void
    {
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse('2026-10-17T12:00:00+06:00'));
        try {
            $this->assertFalse(BotanicalHomeContent::saleHasEnded(['ends_at' => '']));
            $this->assertFalse(BotanicalHomeContent::saleHasEnded(['ends_at' => '2026-10-17T12:01']));
            $this->assertTrue(BotanicalHomeContent::saleHasEnded(['ends_at' => '2026-10-17T11:59']));
            $this->assertTrue(BotanicalHomeContent::saleHasEnded(['ends_at' => '2026-10-17T12:00']));
        } finally {
            \Carbon\Carbon::setTestNow();
        }
    }

}
