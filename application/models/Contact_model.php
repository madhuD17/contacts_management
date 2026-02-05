<?php
class Contact_model extends CI_Model
{
    private $table = 'contacts';

    public function get_contact_data($data, $type='all')
    {
        if(!empty($data['search']))
        {
            $this->db->like('name', $data['search']);
            $this->db->or_like('email', $data['search']);
            $this->db->or_like('phone', $data['search']);
        }

        if($type != 'count')
        {
            $response = $this->db
                    ->order_by($data['sortField'], $data['sortDir'])
                    ->limit($data['size'], $data['offset'])
                    ->get($this->table)
                    ->result();
        }
        else
        {
            $response = $this->db->count_all_results($this->table);
        }

        return $response;
    }

    public function get_custom_field_values($contactId=null)
    {
        $this->db->select('cf.id, cf.field_label, cf.field_name, cf.field_type, cf.is_required, ccv.field_value');
        $this->db->from('contact_custom_field_values ccv');
        $this->db->join('custom_fields cf', 'cf.id = ccv.custom_field_id');
        $this->db->where('ccv.contact_id', $contactId);
        return $this->db->get()->result();
    }

    public function delete_contact($contactId)
    {
        $this->db->trans_start();
        // Delete custom field values
        $this->db->where('contact_id', $contactId)
                ->delete('contact_custom_field_values');

        // Delete contact
        $this->db->where('id', $contactId)
                ->delete('contacts');
                
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
