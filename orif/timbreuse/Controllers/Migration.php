<?php


namespace Timbreuse\Controllers;

use CodeIgniter\Config\Services;

class Migration extends \CodeIgniter\Controller
{
    public function index()
    {
        echo view("Timbreuse\migrationIndex");
    }

    public function init()
    {
        if ($this->request->getPost('password') !== 'a') {
            return $this->response->setStatusCode('401');
        }
        $file = fopen(WRITEPATH . 'appStatus.json', 'r+');
        $initDatas = fread($file, 100);
        if ((json_decode($initDatas, true))['initialized'] !== false) {
            return $this->response->setStatusCode('400');
        }
        $this->response->setStatusCode('201')->send();
        $migrate = Services::migrations();
        try {
            $migrate->setNamespace('User');
            $migrate->latest();
            $migrate->setNamespace('Timbreuse');
            $migrate->latest();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        fclose($file);
        fwrite(fopen(WRITEPATH . 'appStatus.json', 'w+'),
            json_encode(['initialized' => true]));
        unlink((new \ReflectionClass(
            '\Timbreuse\Controllers\Migration'))->getFileName());
        unlink(ROOTPATH . 'orif/Timbreuse/Views/migrationindex.php');
        return $this->response->setStatusCode(200);
    }
}






