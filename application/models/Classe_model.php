<?php

/**
 * Class Classe_model
 * DB interaction with Classe table, CRUD are present and other useful function
 */
class Classe_model extends CI_Model
{
    /**
     * @var string The table name
     */
    private $table = 'Classe';

    public function __construct()
    {
        parent::__construct();
    }

    public function add(string $libelle): bool
    {
        return $this->db->insert($this->table,array('libelle'=>$libelle));
    }

    public function get(array $data): ?array
    {
        if (isset($data['id']) || isset($data['libelle'])){
            return $this->db->select()
                ->from($this->table)
                ->where($data)
                ->get()
                ->result_array();
        }
        return null;
    }

    public function del(string $classId): bool
    {
        if (isset($classId)){
            return $this->db->where(array('id'=>$classId))->delete($this->table);
        }
    }

    public function set(array $data): bool
    {
        if (isset($data['id'])){
            $id = $data['id'];
            unset($data['id']);
        }else{
            return false;
        }

        return $this->db->where(array('id'=>$id))->update($this->table,$data);

    }

    public function getAll()
    {
        return $this->db->select()
                        ->from($this->table)
                    ->get()
                    ->result_array();
    }

    public function exist(string $libelle): bool
    {
        return (count($this->db->select()->from($this->table)->where('libelle LIKE "'.$libelle.'"')->get()->result_array()) > 0);
    }

    public function search(string $libelle): ?array
    {
        return $this->db->select()->from($this->table)->where('libelle LIKE "%'.$libelle.'%"')->get()->result_array();
    }

    /**
     * Return the number of child assigned to the given classe id
     * @param string $classId The classe where to find belonging childs
     * @return int
     */
    public function assignedChild(string $classId): int
    {
        return count($this->db->select()->from('Eleve')->where(array('classe'=>$classId))->get()->result_array());
    }
}