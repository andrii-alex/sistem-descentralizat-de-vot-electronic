<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // $db = \Config\Database::connect();
        // $results = $db->query('SELECT * FROM proba')->getResultArray();
        // var_dump($results[0]['Nume']);
        return view('welcome_message');
    }
}
