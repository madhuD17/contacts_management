<?php
class CustomField_model extends CI_Model
{
    public function get_values_by_contact($contact_id)
    {
        $this->db->select('custom_fields.field_name, contact_custom_field_values.field_value');
        $this->db->from('contact_custom_field_values');
        $this->db->join('custom_fields','custom_fields.id = contact_custom_field_values.custom_field_id');
        $this->db->where('contact_id', $contact_id);
        return $this->db->get()->result();
    }

    public function insert_custom_field_values($contact_id, $customFields)
    {
        foreach ($customFields as $field_id => $value) {
            $this->db->insert('contact_custom_field_values', [
                'contact_id' => $contact_id,
                'custom_field_id' => $field_id,
                'field_value' => $value
            ]);
        }
    }

    public function delete_custom_field($fieldId)
    {
        $this->db->trans_start();
        // Delete custom field values
        $this->db->where('custom_field_id', $fieldId)
                ->delete('contact_custom_field_values');

        // Delete contact
        $this->db->where('id', $fieldId)
                ->delete('custom_fields');
                
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
