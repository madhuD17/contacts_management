<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MergeContacts extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('Contact_model', 'contact_model');
        $this->table = 'contacts';
    }

    public function get_master_contacts()
    {
        $data['id'] = $this->input->post('id');
        $data['contacts'] = $this->common_model->select_all_where($this->table, ['id !=' => $data['id'], 'status' => 'active']);
        $html = $this->load->view('contacts/contact_merge_modal', $data, true);
        echo json_encode(['status' => 'success', 'html' => $html]);
        exit;
    }

    public function merge_contact()
    {
        $masterId = trim($this->input->post('master_id'));
        $secondaryId = trim($this->input->post('secondary_id'));

        $fields = ['email', 'merged_emails','phone','merged_phones'];

        $master    = $this->common_model->select_fields_where('contacts', $fields, ['id' => $masterId]);
        $secondary = $this->common_model->select_fields_where('contacts', $fields, ['id' => $secondaryId]);

        $mergedEmails = $this->getEmailToMerge($master, $secondary);
        $mergedPhones = $this->getPhoneToMerge($master, $secondary);

        $this->db->trans_start();

        //  update in db
        $this->common_model->update(
            'contacts',
            $masterId,
            [
                'merged_emails' => $mergedEmails,
                'merged_phones' => $mergedPhones
            ]
        );
        
        $this->mergeCustomFields($masterId, $secondaryId);
        $this->common_model->update(
            'contacts',
            $secondaryId,
            [
                'master_id' => $masterId,
                'status' => 'merged'
            ]
        );
        
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            echo json_encode(['status' => 'success']);
        }
    }

    public function getEmailToMerge($master, $secondary)
    {
        $emails = [];

        //  Secondary email
        if (!empty($secondary->email)) {
            $emails[] = strtolower(trim($secondary->email));
        }

        //  Secondary merged emails
        if (!empty($secondary->merged_emails)) {
            $emails = array_merge($emails, get_json_decode($secondary->merged_emails));
        }

        //  Master merged emails
        if (!empty($master->merged_emails)) {
            $emails = array_merge($emails, get_json_decode($master->merged_emails));
        }

        //  get unique emails
        $emails = array_unique(
            array_filter(
                array_map(fn($e) => strtolower(trim($e)), $emails)
            )
        );

        //  Remove primary email
        if (!empty($master->email)) {
            $emails = array_diff($emails, [strtolower(trim($master->email))]);
        }

        return !empty($emails) ? json_encode(array_values($emails)) : null;

    }

    public function getPhoneToMerge($master, $secondary)
    {
        $phones = [];

        //  Secondary email
        if (!empty($secondary->phone)) {
            $phones[] = trim($secondary->phone);
        }

        //  Secondary merged phones
        if (!empty($secondary->merged_phones)) {
            $phones = array_merge($phones, get_json_decode($secondary->merged_phones));
        }

        //  Master merged phones
        if (!empty($master->merged_phones)) {
            $phones = array_merge($phones, get_json_decode($master->merged_phones));
        }

        //  get unique phones
        $phones = array_unique(array_filter(array_map('trim', $phones)));

        //  Remove primary phone
        if (!empty($master->phone)) {
            $phones = array_diff($phones, [trim($master->phone)]);
        }

        return !empty($phones) ? json_encode(array_values($phones)) : null;
    }

    /**
     * Merge custom fields from secondary contact into master contact
     */
    public function mergeCustomFields($masterId, $secondaryId)
    {
        // Fetch master custom fields
        $master_fields = $this->common_model->select_all_where_array('contact_custom_field_values', ['contact_id' => $masterId]);

        // Fetch secondary custom fields
        $secondary_fields = $this->common_model->select_all_where_array('contact_custom_field_values', ['contact_id' => $secondaryId]);

        // index master fields by custom_field_id
        $master_index = [];
        foreach ($master_fields as $field) {
            $master_index[$field['custom_field_id']] = $field;
        }

        // Process secondary fields
        foreach ($secondary_fields as $sec_field) {

            $field_id = $sec_field['custom_field_id'];
            $sec_value = trim((string) $sec_field['field_value']);

            // Case A: Master does NOT have this field → INSERT
            if (!isset($master_index[$field_id])) {

                $this->common_model->insert('contact_custom_field_values', [
                    'contact_id'      => $masterId,
                    'custom_field_id' => $field_id,
                    'field_value'     => $sec_value
                ]);

            } else {
                // Case B: Both have the field
                $master_value = trim((string) $master_index[$field_id]['field_value']);

                // If values differ and secondary value is not empty
                if ($sec_value !== '' && $master_value !== $sec_value) {

                    // Merge strategy: keep master, append secondary
                    $merged_value = $master_value . ' | ' . $sec_value;

                    // $this->common_model->update('contact_custom_field_values', $master_index[$field_id]['id'], ['field_value' => $merged_value]);
                }
            }
        }
    }
}
