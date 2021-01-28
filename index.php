<?php

include_once __DIR__.'/vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;


class UserInfo
{
    private string $base_url = 'https://22zz.ru/api-test/';

    public function exec($a)
    {
        if (!method_exists($this, $a)) {
            throw new Exception('error');
        }

        return $this->$a();
    }

    public function get_token()
    {
      $client = new GuzzleHttp\Client();
      //получаем токен
      //https://22zz.ru/api/auth?login=test&password=test1
      $response = $client->request('GET', $this->base_url.'auth', [
      'query' => ['login' => 'test', 'password' => 'test1']
      ]);
      //echo $response->getStatusCode(); //200
      //echo $response->getReasonPhrase(); //OK
      //echo $response->getBody(); //[{"status":"OK"},{"token":"180CrQ5cFs4N9b4jvr0suoe0CuSsdwuAd7JHqscBwGccysAIxbawt35n61UjP5uE"}]
      $data = (json_decode($response->getBody(), true));
      $token = $data[1]['token'];
      return $token;
    }

    public function get_user()
    {
      $client = new GuzzleHttp\Client();
      //получаем данные пользователя
      //https://22zz.ru/api/get-user/test?token=180CrQ5cFs4N9b4jvr0suoe0CuSsdwuAd7JHqscBwGccysAIxbawt35n61UjP5uE
      $response = $client->request('GET', $this->base_url.'get-user/test', [
      'query' => ['token' => $this->get_token()]
      ]);
      //в формате json:
      //echo $response->getBody();
      $data = (json_decode($response->getBody(), true));
      $id = $data[1]['id'];
      $name = $data[1]['name'];
      $first_name = $data[1]['first_name'];
      $last_name = $data[1]['last_name'];
      $email = $data[1]['email'];

      echo "id:".$id."</br>
           name:".$name."</br>
           first_name:".$first_name."</br>
           last_name:".$last_name."</br>
           email:".$email."</br>";
    }

    public function update_user()
    {
      $client = new GuzzleHttp\Client();
      //исправляем данные ползователя
      $link = 'https://22zz.ru/api-test/user/7/update?token='.$this->get_token();

      $data = [
        'name' => 'Natashka',
        'activated' => '1',
        'blocked' => 'true'
      ];

      try {
          $response = $client->request('PUT', $link, ['form_params' => $data]);
          echo json_encode(array(
                      "status" => $response->getReasonPhrase(),
                      "body"   => (string) $response->getBody()
                  ));
      } catch (GuzzleHttp\Exception\GuzzleException | Exception $e) {
          echo $e->getMessage() . \PHP_EOL;
      }
      

    }


}



try {
    $UserInfo = new UserInfo();
    echo $UserInfo->exec('update_user');
} catch (Exception $e) {
    echo $e->getMessage();
}
