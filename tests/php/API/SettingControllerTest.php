<?php

namespace Tests\API\Resources;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Util\Errors;
use App\Util\Utils;

class SettingControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;

    public function setUp()
    {
        parent::setUp();

        $this->admin = factory(User::class)->create([
            'type_id' => 1
        ]);
    }

    public function testPOSTSettingsWithValidForm()
    {
        $response = $this->actingAs($this->admin)->json(
                'POST',
                '/data/settings',
                [
                    'password' => 'aaaaaa',
                    'password_confirmation' => 'aaaaaa',
                ]
            );

        $response
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'password' => true,
            ]);

        $json = json_decode($response->getContent());

        $password = User::find($this->admin->id)->password;

        $this->assertTrue(Hash::check('aaaaaa', $password));
    }

    public function testPOSTSettingsWithInvalidConfirmation()
    {
        $response = $this->actingAs($this->admin)->json(
                'POST',
                '/data/settings',
                [
                    'password' => 'aaaaaa',
                    'password_confirmation' => 'aaaaaab',
                ]
            );

        $response
            
        ->assertHeader('Content-Type', 'application/json')
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => __('validation.message'),
                'errors' => [
                    'password' => [ __('validation.confirmed', [
                        'attribute' => __('validation.attributes.password'),
                    ]) ],
                ],
            ]);

        $json = json_decode($response->getContent());

        $password = $this->admin->password;
        
        $this->assertFalse(Hash::check('aaaaaa', $password));
    }

    public function testPOSTSettingsWithInvalidCharNumber()
    {
        $response = $this->actingAs($this->admin)->json(
                    'POST',
                    '/data/settings',
                    [
                        'password' => 'aaaa',
                        'password_confirmation' => 'aaaa',
                    ]
                );

        $json = json_decode($response->getContent());

        $response
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => __('validation.message'),
                'errors' => [
                    'password' => [ __('validation.min.string', [
                        'attribute' => __('validation.attributes.password'),
                        'min' => 6,
                    ]) ],
                ],
            ]);

        $password = User::find($this->admin->id)->password;

        $this->assertFalse(Hash::check('aaaa', $password));
    }

    public function testPOSTSettingsWithoutPassword()
    {
        $response = $this->actingAs($this->admin)->json(
            'POST',
            '/data/settings'
        );

        $response
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([]);

        $json = json_decode($response->getContent());
        
        $password = User::find($this->admin->id)->password;

        $this->assertTrue(Hash::check('secret', $password));


        $this->admin->delete();
    }
}
