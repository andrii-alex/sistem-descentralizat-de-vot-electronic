<?php

namespace App\Controllers;

class EvuidGenerator extends BaseController
{
  public function check_token(){
    $token = $this->request->getJsonVar("token");

    $builder = $this->db->table('voters');
    $builder->where('token', $token);

    if($builder->countAllResults() == 0)
    {
      $this->response->setStatusCode(500);
      $this->response->setJSON(array(
          'message' => 'The token is invalid!'
      ))->send();
        exit();
    }

    $voter = $builder->get()->getRow();

    if($voter->voted != 0)
    {
      $this->response->setStatusCode(500);
      $this->response->setJSON(array(
          'message' => 'Already voted!!'
      ))->send();
        exit();
    }

    $this->response->setStatusCode(200);
    $this->response->setJSON(array(
        'message' => 'The token is valid!',
        'voter' => json_encode($voter)
    ))->send();
      exit();

  }

  public function send_email_voting_paper(){
    $user_email = $this->request->getPost("email");
    $evuid = $this->request->getPost("evuid");
    $signature = $this->request->getPost("signature");

    $email = \Config\Services::email();

    $email->setTo($user_email);
    $email->setSubject('Voting Paper generated!');

    $data = [
      'link' => "https://evoting.andrii.ro/#/vote",
      'evuid' => $evuid,
      'signature' => $signature
    ];
    
    $html_message = view('mail/voting_paper', $data);

    $email->setMessage($html_message);

    $email->send();


    $builder_request = $this->db->table('voters');
    $builder_request->where('email', $user_email);
    $builder_request->update(array(
        'voted' => 1
    ));
  }
}