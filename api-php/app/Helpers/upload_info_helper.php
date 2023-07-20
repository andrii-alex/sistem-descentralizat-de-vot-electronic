<?php
class UploadHelper
{
    public $FileName = "";
    public $Url      = "";
    public $LocalFullPath = "";
    public $HasError = false;
    public $Error    = "";
    public $MimeType = "";

    private $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
    }

    public function upload_image( $path, $field_name = 'file' )
    {
        return $this->upload($path, $field_name, 'gif|jpg|png|jpeg');
    }

    public function upload_image_and_pdf( $path, $field_name = 'file' )
    {
        return $this->upload($path, $field_name, 'gif|jpg|png|jpeg|pdf');
    }

    public function upload_csv( $path, $field_name = 'file' )
    {
        return $this->upload($path, $field_name, 'csv');
    }

    public function upload( $path, $field_name = 'file', $allowed_types = 'gif|jpg|png|jpeg' )
    {
        $hasError = false;
        $error 	  = array();

        $rnd1      = rand(0,999999);
        $rnd2      = rand(0,999999);
        $newFileName = date("Y-m-d_H-i-s-$rnd1-$rnd2");


        $config['upload_path'	] = "{$path}/";
        $config['allowed_types'	] = $allowed_types;
        $config['file_name'     ] = $newFileName;

        $this->ci->load->library('upload', $config);

        if ( ! $this->ci->upload->do_upload( $field_name ))
        {
            $error 		= $this->ci->upload->display_errors();
            $hasError 	= true;
        }
        else
        {
            $data 	    	     = array('upload_data' => $this->ci->upload->data());
            $this->FileName      = $data['upload_data']['file_name'];
            $this->Url 	         = base_url()."{$path}/{$this->FileName}";
            $this->LocalFullPath = "{$path}/{$this->FileName}";
            $this->MimeType      = mime_content_type("{$path}/{$this->FileName}");
        }
        $this->HasError	 = $hasError;
        $this->Error 	 = $error;
    }
}