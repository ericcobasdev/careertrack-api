<?php

namespace Tests\Feature\Documentation;

use Tests\TestCase;

class OpenApiDocumentationTest extends TestCase
{
    public function test_open_api_ui_documentation_is_available(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $response = $this->get('/docs/api');

        $response->assertOk();
    }

    public function test_open_api_json_documentation_is_available(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $response = $this->getJson('/docs/api.json');

        $response
            ->assertOk()
            ->assertJsonPath('info.title', 'CareerTrack API')
            ->assertJsonPath('info.version', '1.0.0')
            ->assertJsonPath('info.description', 'REST API for tracking job applications');
    }
}
