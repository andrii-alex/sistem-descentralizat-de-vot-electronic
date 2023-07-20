<?php

namespace App\Controllers;

require __DIR__ . '/../../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Aws\Rekognition\RekognitionClient;
use Aws\Textract\TextractClient;

class ValidationForm extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function submit_form()
    {
        $first_name     = $this->request->getPost("first_name");
        $last_name      = $this->request->getPost("last_name");
        $county         = $this->request->getPost("county");
        $cnp            = $this->request->getPost("cnp");
        $phone          = $this->request->getPost("phone");
        $email          = $this->request->getPost("email");
        $id_card        = $this->request->getFile("id_card");
        $selfie         = $this->request->getFile("selfie");

        $id_card->move(ROOTPATH . '/public/uploads/ids', $id_card->getRandomName());
        $selfie->move(ROOTPATH . '/public/uploads/selfies', $selfie->getRandomName());
    
        $builder_voter = $this->db->table('voters');
        $builder_voter->where('cnp', $cnp);
        $query_voter = $builder_voter->get();
        $found_voter = $query_voter->getResultArray();

        if(count($found_voter) == 0){
            return $this->response->setJSON(false);
        }

        $found_voter = $found_voter[0];

        $current_time = date('Y-m-d H:i:s');
    
        $data = [
            'id_voter' => $found_voter['id'],
            'time'  => $current_time,
            'status'  => 'unprocessed',
            'img_id' => $id_card->getName(),
            'img_selfie' => $selfie->getName()
        ];
    
        $builder_request = $this->db->table('requests');
        $result = $builder_request->insert($data);
    
        if ($result) {
            $this->send_email_submit_formular($email, $found_voter['token']);
            return $this->response->setJSON(true);
        } else {
            return $this->response->setJSON(false);
        }
    }    

    public function check_cnp(){
        $cnp = $this->request->getPost("cnp");

        $builder_voter = $this->db->table('voters');
        $builder_voter->where('cnp', $cnp);
        $query_voter = $builder_voter->get();
        $found_voter = $query_voter->getResultArray();

        if (count($found_voter) == 0) {
            return $this->response->setJSON(false);
        } else {
            return $this->response->setJSON(true);
        }
    }

    function send_email_submit_formular($user_email, $user_token)
    {
        $email = \Config\Services::email();

        $email->setTo($user_email);
        $email->setSubject('Email Validation Code');
        
        $data = [
            'link' => "https://evoting.andrii.ro/#/evuid/" . $user_token,
        ];
        $html_message = view('mail/request_submitted', $data);

        $email->setMessage($html_message);

        $email->send();
    }

    public function upload_buletin()
    {
        $img_id = $this->request->getFile('img_id');

        $img_id->move(ROOTPATH . '/public/uploads/ids', $img_id->getRandomName());

        $this->response->setStatusCode(200);
        $this->response->setJSON(array(
            'img_id' => $img_id->getName()
        ))->send();
    }

    public function upload_selfie()
    {
        $img_selfie = $this->request->getFile('selfie');

        $img_selfie->move(ROOTPATH . '/public/uploads/selfies', $img_selfie->getRandomName());

        $this->response->setStatusCode(200);
        $this->response->setJSON(array(
            'img_selfie' => $img_selfie->getName()
        ))->send();
    }

    public function decide_request()
    {
        $id_request = $this->request->getJsonVar("id_request");
        $status = $this->request->getJsonVar("request_status");
        $builder_request = $this->db->table('requests');
        $builder_request->where('id', $id_request);
        $builder_request->update(array(
            'status' => $status
        ));

        if ($status == 'accepted') {
            $row = $builder_request->get()->getResultArray()[0];
            $this->generate_user_token($row['id_voter']);
        }

        $this->response->setStatusCode(200);
        $this->response->setJSON(array(
            'message' => 'Request updated!'
        ))->send();
    }

    private function generate_user_token($id_voter)
    {
        helper('text');
        $token = '';
        do {
            $token = random_string('alnum', 32);
            $builder_voter = $this->db->table('voters');
            $builder_voter->where('token', $token);
        } while ($builder_voter->countAllResults() > 0);

        $builder_voter = $this->db->table('voters');
        $builder_voter->where('id', $id_voter);
        $builder_voter->update(array(
            'token' => $token
        ));
    }

    public function send_email()
    {
        $user_email = $this->request->getVar("email");
        $code = random_int(10000000, 99999999);

        $email = \Config\Services::email();

        $email->setTo($user_email);
        $email->setSubject('Email Validation Code');
        
        $html_message = view('mail/validation_code', array(
            'code' => $code
        ));

        $email->setMessage($html_message);

        if ($email->send()) {
            $status_trimitere = true;
        } else {
            $status_trimitere = false;
        }

        $response_data =  [
            'email' => $user_email,
            'code' => $code,
            'status_trimitere' => $status_trimitere
        ];

        $this->response->setStatusCode(200);
        $this->response->setJSON($response_data)->send();
    }

    public function test(){
        // $config = [
        //     'region' => 'us-east-2',
        //     'version' => 'latest',
        //     'credentials' => [
        //         'key' => getenv('ACCESS_KEY_AWS'),
        //         'secret' => getenv('SECRET_KEY_AWS'),
        //     ],
        // ];

        // // $s3Client = new \Aws\S3\S3Client($config);
        // $rekognitionClient = new \Aws\Rekognition\RekognitionClient($config);

        // $image1 = file_get_contents(ROOTPATH . 'public/uploads/selfies/1683696928_07053a4eee22357d1e2f.jpeg');
        // $image2 = file_get_contents(ROOTPATH . 'public/uploads/selfies/1683696928_07053a4eee22357d1e2f.jpeg');

        // $result = $rekognitionClient->compareFaces([
        //     'SimilarityThreshold' => 90,
        //     'SourceImage' => [
        //         'Bytes' => $image1,
        //     ],
        //     'TargetImage' => [
        //         'Bytes' => $image2,
        //     ],
        // ]);

        // print_r($result);

        $data = [
            'link' => "https://evoting.andrii.ro/evuid/",
            'evuid' => "1234567890",
            'signature' => "Andrei",
        ];

        return view('mail/voting_paper', $data);
        
    }

    // public function get_text_from_id_card(){
    //     $id_card = $this->request->getFile('id_card');

    //     print_r($id_card);
    // }

    public function get_text_from_id_card() {
        $idCardImage = $_FILES['id_card'];
        $imageBytes = file_get_contents($idCardImage['tmp_name']['raw']);

        $config = [
            'region' => 'us-east-2',
            'version' => 'latest',
            'credentials' => [
                'key' => getenv('ACCESS_KEY_AWS'),
                'secret' => getenv('SECRET_KEY_AWS'),
            ],
        ];

        $textractClient = new TextractClient($config);
       
        $options = [
            'Document' => [
                'Bytes' => $imageBytes
            ],
            'FeatureTypes' => ['FORMS'], // REQUIRED
        ];
    
        $result = $textractClient->analyzeDocument($options);
    
        $blocks = $result['Blocks'];
        $cnp = '';
    
        foreach ($blocks as $block) {
            if ($block['BlockType'] === 'LINE') {
                $text = $block['Text'];
    
                if ($this->isValidCNP($text)) {
                    $cnp = $text;
                    break;
                }
            }
        }
    
        if ($cnp) {
            return $this->response->setJSON($cnp);
        } else {
            return $this->response->setJSON(false);
        }
    
        exit();
    }

    public function compare_idcard_with_selfie(){
        $id_card = $this->request->getFile('id_card');
        $selfie = $this->request->getFile('selfie');

        $id_card_image = file_get_contents($id_card->getTempName());
        $selfie_image = file_get_contents($selfie->getTempName());

        $config = [
            'region' => 'us-east-2',
            'version' => 'latest',
            'credentials' => [
                'key' => getenv('ACCESS_KEY_AWS'),
                'secret' => getenv('SECRET_KEY_AWS'),
            ],
        ];

        $rekognitionClient = new \Aws\Rekognition\RekognitionClient($config);

        $result = $rekognitionClient->compareFaces([
            'SimilarityThreshold' => 90,
            'SourceImage' => [
                'Bytes' => $id_card_image,
            ],
            'TargetImage' => [
                'Bytes' => $selfie_image,
            ],
        ]);

        $similarity = 0;
        if(isset($result['FaceMatches'][0]['Similarity']))
            $similarity = $result['FaceMatches'][0]['Similarity'];

        if ($similarity >= 90) {
            return $this->response->setJSON(true);
        } else {
            return $this->response->setJSON(false);
        }
    }
    
    // Function to validate the extracted CNP
    private function isValidCNP($cnp) {

        if (strlen($cnp) !== 13) {
            return false;
        }

        if (!ctype_digit($cnp)) {
            return false;
        }

        return true;

    }
    
}
// 