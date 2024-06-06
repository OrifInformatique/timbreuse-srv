<?php


namespace User\Database\Seeds;


class AddUserDatas extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $data = [
            ['fk_user_type'=>1,'username'=>'admin','password'=>'$2y$10$uiZQFFduJk7HjmUlYKY.LesTB5yMfNxYvvI0ceB8daca/3tVOFBSS'],
            ['fk_user_type'=>2,'username'=>'utilisateur','password'=>'$2y$10$11wIuR3FnfWwTpfyJ9WCz.E3KErvb.i.Q2Wef6XMUZHTXUlW0FhJm']
        ];
        foreach($data as $row)
        $this->db->table('user')->insert($row);
    }
}