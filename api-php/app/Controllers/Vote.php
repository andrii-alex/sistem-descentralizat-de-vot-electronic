<?php

namespace App\Controllers;

class Vote extends BaseController
{
    public function get_candidates(){
        $candidates_builder = $this->db->table('candidates');
        $candidates = $candidates_builder->get()->getResult();

        return $this->response->setJSON(['candidates' => $candidates]);
    }
}