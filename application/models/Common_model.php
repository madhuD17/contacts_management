<?php
class Common_model extends CI_Model
{
    public function get_all($table='')
    {
        return $this->db->order_by('id', 'DESC')->get($table)->result();
    }

    public function get_by_id($table='', $id=null)
    {
        return $this->db->get_where($table, ['id' => $id])->row();
    }

    public function select_where($table='', $where=[])
    {
        return $this->db->get_where($table, $where)->row();
    }

    public function insert($table='', $data=[])
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function update($table='', $id=null, $data=[])
    {
        return $this->db->where('id', $id)->update($table, $data);
    }

    public function insert_all($table='', $data=[])
    {
        return $this->db->insert_batch($table, $data);
    }

    public function update_all($table='', $data=[], $key='id')
    {
        return $this->db->update_batch($table, $data, $key);
    }

    public function delete($table='', $id=null)
    {
        return $this->db->delete($table, ['id' => $id]);
    }

    public function select_all_where($table='', $where=[])
    {
        return $this->db->order_by('id', 'DESC')->get_where($table, $where)->result();
    }

    public function select_all_where_array($table='', $where=[])
    {
        return $this->db->where($where)->get($table)->result_array();
    }

    public function select_fields_where($table='', $fields='', $where=[])
    {
        return $this->db
            ->select($fields)
            ->from($table)
            ->where($where)
            ->get()
            ->row();
    }

    public function count_all($table='', $where=[])
    {
        if(!empty($where)) {
            $this->db->where($where);
        }
        return $this->db->count_all_results($table);
    }
}
