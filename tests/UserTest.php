<?php

use PHPUnit\Framework\TestCase;
require __DIR__.'/../vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class UserTest extends TestCase
{
  function client() {
        return $client = new GuzzleHttp\Client;
  }

   private string $base_url = 'https://22zz.ru/api-test/';

   public function test_user_get_token()
   {
         $response = $this->client()->request('GET', $this->base_url.'auth', [
         'query' => [
           'login' => 'test',
           'password' => 'test1']
         ]);

         $this->assertNotEmpty($response->getBody());
  }

   public function test_user_get_userinfo_from_token()
   {
         $response = $this->client()->request('GET', $this->base_url.'auth', [
         'query' => [
           'login' => 'test',
           'password' => 'test1']
         ]);
         $data = (json_decode($response->getBody(), true));
         $token = $data[1]['token'];
         $userinfo = $this->client()->request('GET', $this->base_url.'get-user/test', [
         'query' => ['token' => $token]
         ]);
         $data = (json_decode($userinfo->getBody(), true));
         $first_name = $data[1]['first_name'];
         //$last_name = $data[1]['last_name'];
         $this->assertNotEmpty($first_name);
   }

   public function test_user_update_userinfo_from_token()
   {
     $token = '180CrQ5cFs4N9b4jvr0suoe0CuSsdwuAd7JHqscBwGccysAIxbawt35n61UjP5uE';
     $link = 'https://22zz.ru/api-test/user/7/update?token='.$token;

     $data = [
       //'signup_ip_address' => 'test',
       'name' => 'N',
       'activated' => '5',
       'blocked' => 't'
     ];

     $response = $this->client()->request('PUT', $link, ['form_params' => $data]);
     //проверяем  исправленные данные
     $db = new PDO('mysql:host=localhost;dbname=laravelAuth', 'root', 'andreipunt90');
     $stmt = $db->prepare("SELECT first_name FROM users WHERE `name` = ?");
     $stmt->execute(['test']);
     $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

     $this->assertEquals($data[0]['first_name'],'N');

        //восттанавливаем данные
        file_get_contents('https://22zz.ru/guzzle/');
   }


}
