<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function login()
    {
        try {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');

            // Check if user exists
            $query = $this->db->table('admins')->where('username', $username)->get();
            $user = $query->getResultArray();
            if (count($user) == 0) {
                return $this->response->setStatusCode(404)->setJSON(['message' => 'User not found']);
            }
            $user = $user[0];

            // Check if password is correct
            $isMatch = password_verify($password, $user['password']);
            if (!$isMatch) {
                return $this->response->setStatusCode(401)->setJSON(['message' => 'Invalid credentials']);
            }

            // Create and send JWT token
            $token = $this->createNewToken($user['id']);

            $builder_insert_token = $this->db->table('auth_tokens');
            $builder_insert_token->insert([
                'id_admin' => $user['id'],
                'token' => $token
            ]);

            return $this->response->setJSON(['token' => $token]);
        } catch (Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['message' => $e->getMessage()]);
        }
    }

    // public function register(){
    //     $data = [
    //         'username' => 'andriialexandru20',
    //         'password' => password_hash('andrii00', PASSWORD_DEFAULT)
    //     ];

    //     $builder = $this->db->table('admins');
    //     $builder->insert($data);
    // }

    private function createNewToken($id_user)
    {
        $now = date("Y-m-d H:i:s");
        return md5($id_user . "-" . date("Y-m-d H:i:s"));
    }


    public function getAllRequests(){
        $query = $this->db->table('requests')->get();
        $requests = $query->getResultArray();

        foreach($requests as $key => $request){
            $query_voter_info = $this->db->table('voters')->where('id', $request['id_voter'])->get();
            $requests[$key]['voter_info'] = $query_voter_info->getResultArray();

            if(count($requests[$key]['voter_info']) != 0)
                $requests[$key]['voter_info'] = $requests[$key]['voter_info'][0];
        }

        return $this->response->setJSON(['requests' => $requests]);
    }
}
