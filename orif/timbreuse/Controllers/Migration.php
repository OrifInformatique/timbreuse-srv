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
        if ($this->request->getPost('password') !== 'rFhJe3tKq4iYAYa4nv48') {
            return $this->response->setStatusCode('401');
        }
        $filePath = WRITEPATH . 'appStatus.json';
        $appStatus = $this->get_app_status($filePath);
        if ($appStatus['initialized'] !== false) {
            return $this->response->setStatusCode('400');
        }
        $this->response->setStatusCode('201')->send();
        $this->invoke_migration('User', 'Timbreuse');
        fwrite(fopen($filePath, 'w'),
            json_encode(['initialized' => true]));
        $this->delete_files();
        return $this->response->setStatusCode(200);
    }

    protected function get_app_status($filePath) {
        if (file_exists($filePath)){
            $file = fopen($filePath, 'r');
            $initDatas = fread($file, 100);
            fclose($file);
            $appStatus = json_decode($initDatas, true);
        } else {
            $appStatus['initialized'] = false;
        }
        return $appStatus;
    }

    protected function delete_files() {
        unlink((new \ReflectionClass(
            '\Timbreuse\Controllers\Migration'))->getFileName());
        unlink(ROOTPATH . 'orif/Timbreuse/Views/migrationindex.php');
    }

    protected function invoke_migration(...$namespaces) {
        try {
            $migrate = Services::migrations();
            foreach ($namespaces as $namespace) {
                $migrate->setNamespace($namespace);
                $migrate->latest();
            }
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

}






