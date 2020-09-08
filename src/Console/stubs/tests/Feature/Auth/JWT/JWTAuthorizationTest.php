<?php

namespace Tests\Feature;

use App\Http\Controllers\AuthController;
use App\User;

use Tests\TestCase;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;


class JWTAuthorizationTest extends TestCase
{
    use RefreshDatabase;
    protected $baseURL;

    public function setUp() : void
    {
        parent::setUp();

        $this->baseURL = '/api';

        $user = new User([
             'email'    => 'test@email.com',
             'password' => bcrypt('12345678'),
             'name' => 'Cool Guy!'
         ]);

        $user->save();
    }

     /**
     * Set the currently logged in user for the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string|null $driver
     * @return $this
     */
    public function actingAs($user, $driver = null)
    {
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', "Bearer {$token}");
        parent::actingAs($user);

        return $this;
    }

    /** @test ***/
    public function a_user_can_register()
    {

        $URL  = $this->baseURL.'/users';

        $response = $this->postJson($URL, [
            'email'=>'testNew@email.com',
            'password'=> 'Password-123-Secret',
            'name' => 'Cool Guy!'
        ]);

        $response
        ->assertStatus(201)
        ->assertJsonStructure([
            'jwt', 'refresh_token', 'token_type', 'expires_in'
        ]);
    }

    /** @test */
    public function a_user_cannot_register_without_name()
    {
        $URL  = $this->baseURL.'/users';

        $response = $this->postJson($URL, [
            'email'=>'testNew@email.com',
            'password'=> '12345678',
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message', 'errors'
        ]);
    }

    /** @test */
    public function a_user_cannot_register_without_password()
    {
        $URL  = $this->baseURL.'/users';

        $response = $this->postJson($URL, [
            'email'=>'testNew@email.com',
            'name' => 'Cool Guy!'
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message', 'errors'
        ]);
    }

    /** @test */
    public function a_user_cannot_register_without_email()
    {
        $URL  = $this->baseURL.'/users';

        $response = $this->postJson($URL, [
            'password'=> '12345678',
            'name' => 'Cool guy!'
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message', 'errors'
        ]);
    }

    /** @test */
    public function a_user_cannot_register_with_already_registered_email()
    {
        $URL  = $this->baseURL.'/users';

        $response = $this->postJson($URL, [
            'email'=>'test@email.com',
            'password'=> '12345678',
            'name' => 'Cool Guy!'
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message', 'errors'
        ]);
    }

    /** @test */
    public function a_user_cannot_register_without_data()
    {
        $URL  = $this->baseURL.'/users';
        $response = $this->postJson($URL, []);

        $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message', 'errors'
        ]);
    }

    /** @test */
    public function a_user_password_should_be_atleast_8_in_length()
    {
        $URL  = $this->baseURL.'/users';
        $response = $this->postJson($URL, [
            'email'=>'test@email.com',
            'password'=> '123456',
            'name' => 'Cool Guy!'
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message', 'errors'
        ]);
    }

    /** @test ***/
    public function a_user_can_login()
    {

        $URL  = $this->baseURL.'/access-tokens';

        $response = $this->postJson($URL, [
            'email'    => 'test@email.com',
            'password' => '12345678',
        ]);

        $response
        ->assertStatus(201)
        ->assertJsonStructure([
            'jwt', 'refresh_token', 'token_type', 'expires_in'
        ]);
    }

    /** @test ***/
    public function a_user_cannot_login_with_wrong_password()
    {
        $URL  = $this->baseURL.'/access-tokens';
        $response = $this->postJson($URL, [
            'email'    => 'test@email.com',
            'password' => 'notlegitpassword'
        ]);

        $response
        ->assertStatus(401)
        ->assertJsonStructure([
            'error',
        ]);
    }

    /** @test ***/
    public function a_password_required_for_login()
    {
        $URL  = $this->baseURL.'/access-tokens';
        $response = $this->postJson($URL, [
            'email'    => 'test@email.com'
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'errors',
        ]);
    }

    /** @test */
    public function a_user_can_fetch_its_details(){

        $URL  = $this->baseURL.'/me';

        $user = User::first();
        $response = $this->actingAs($user)->getJson($URL);

        $response->assertJsonStructure([
            'email','name','avatar_url'
        ]);

        $response->assertStatus(200);

    }

    /** @test */
    public function a_user_cannot_fetch_its_details_wihtout_authentication(){

        $URL  = $this->baseURL.'/me';

        $response = $this->getJson($URL);

        $response->assertExactJson([
            'message' => 'Unauthenticated.'
        ]);

        $response->assertStatus(401);

    }


    /** @test ***/
    public function a_user_can_logout()
    {

        $URL  = $this->baseURL.'/access-tokens';
        $user = User::first();
        $token = Auth::login($user);

        (new AuthController)->respondWithToken($token);

        $response = $this->actingAs($user)->deleteJson($URL,[
            'refresh_token' => $user->token->token
        ]);

        $response->assertStatus(204);
    }

    /** @test ***/
    public function a_user_cannot_logout_with_invalid_token()
    {
        $URL  = $this->baseURL.'/access-tokens';
        $user = User::first();
        $token = Auth::login($user);

        (new AuthController)->respondWithToken($token);

        $response = $this->actingAs($user)->deleteJson($URL,[
            'refresh_token' => Str::random(100)
        ]);

        $response->assertStatus(422);
    }

    /** @test ***/
    public function a_user_cannot_logout_without_authentication()
    {
      // $this->withoutExceptionHandling();

        $URL  = $this->baseURL.'/access-tokens';

        $user = User::first();
        $token = Auth::login($user);

        (new AuthController)->respondWithToken($token);

        $token = $user->token->token;
        // logout User, but we have valid token, since its not deleted in logout.
        Auth::logout();

        $response = $this->deleteJson($URL,[
            'refresh_token' => $token
        ]);

        $response->assertExactJson([
            'message' => 'Unauthenticated.'
        ]);

        $response->assertStatus(401);

    }

}
