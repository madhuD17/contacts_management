<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contacts extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('Contact_model', 'contact_model');
        $this->load->library('upload');
        $this->table = 'contacts';
    }

    public function index()
    {
        $this->load->view('contacts/index');
    }

    public function index_data()
    {
        // Sorting
        $sorters = $this->input->post('sort');
        $data = [
            'search'    => $this->input->post('search'),
            'page'      => $this->input->post('page') ?? 1,
            'size'      => $this->input->post('size') ?? 10,
            'sortField' => $sorters[0]['field'] ?? 'id',
            'sortDir'   => $sorters[0]['dir'] ?? 'desc'
        ];
        $data['offset'] = ($data['page'] - 1) * $data['size'];
        $result['data'] = $this->contact_model->get_contact_data($data);
        $total = $this->contact_model->get_contact_data($data, 'count');
        $result['last_page'] = ceil($total / $data['size']);
        echo json_encode([
            'data' => $result,
            'last_page' => ceil($total / $data['size'])
        ]);
    }

    public function create()
    {
        $data['action'] = 'add';
        $data['custom_fields'] = $this->common_model->get_all('custom_fields');
        $this->load->view('contacts/add', $data);
    }

    public function edit($id=null)
    {
        $data['action'] = 'edit';
        $data['contact'] = $this->common_model->get_by_id($this->table, $id);
        $data['custom_fields'] = $this->contact_model->get_custom_field_values($id);
        $this->load->view('contacts/add', $data);
    }

    public function save()
    {
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('phone', 'Phone', 'required');
        $this->form_validation->set_rules('gender', 'Gender', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $message = [
                'status'  => 'error',
                'message' => 'Please fill all required fields',
            ];
        } else {
            // Upload profile image
            $profileImage = '';
            if (!empty($_FILES['profile_image']['name'])) {
                $profileImage = $this->uploadFile('profile_image','./uploads/profile/');
            }

            // Upload document
            $document = '';
            if (!empty($_FILES['document_file']['name'])) {
                $document = $this->uploadFile('document_file','./uploads/document/');
            }
            $inputData = $this->input->post();
            $contactId = trim($inputData['id']);

            $contactData = [
                'name'          => trim($inputData['name']),
                'email'         => trim($inputData['email']),
                'phone'         => $inputData['phone'],
                'gender'        => $inputData['gender'],
                'profile_image' => $profileImage,
                'document_file' => $document
            ];
        
            if($inputData['action'] == 'edit') {
                $this->common_model->update($this->table, $contactId, $contactData);
                $message = [
                    'status'  => 'success',
                    'message' => 'Contact updated succesfully.',
                ];
            } else {
                $contactId = $this->common_model->insert($this->table, $contactData);
                $message = [
                    'status'  => 'success',
                    'message' => 'Contact added succesfully.',
                ];
            }
            if($this->input->post('custom_fields'))
            {
                $updateData = $newData = [];
                $customFields = $this->input->post('custom_fields');
                foreach ($customFields as $fieldId => $value) {
                    if ($value != '') {
                        $whereData = [
                            'contact_id'      => $contactId,
                            'custom_field_id' => $fieldId
                        ];
                        $existingRecords = $this->common_model->select_where('contact_custom_field_values', $whereData);
                        if(!empty($existingRecords) && $existingRecords->id > 0) {
                            $updateData[$existingRecords->id] = [
                                'id'              => $existingRecords->id,
                                'field_value'     => $value
                            ];
                        } else {
                            $newData[$fieldId] = [
                                'contact_id'      => $contactId,
                                'custom_field_id' => $fieldId,
                                'field_value'     => $value
                            ];
                        }
                    }
                }
                // update batch
                if(!empty($updateData) && count($updateData) > 0) {
                    $this->common_model->update_all('contact_custom_field_values', $updateData, 'id');
                }
                // insert batch
                if(!empty($newData) && count($newData) > 0) {
                    $this->common_model->insert_all('contact_custom_field_values', $newData);
                }
            }
        }
        echo json_encode($message);
        exit;
    }

    private function uploadFile($field, $path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $config['upload_path']   = $path;
        $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx';
        $config['encrypt_name']  = false;
        $config['file_name'] = $field.'_'. uniqid();

        $this->upload->initialize($config);
        $this->upload->do_upload($field);
        return $this->upload->data('file_name');
    }

    public function delete()
    {
        $contactId = (int) $this->input->post('id');
        if (!$contactId) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid contact ID']);
            exit;
        }
        $result = $this->contact_model->delete_contact($contactId);
        if ($result === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
        } else {
            echo json_encode(['status' => 'success']);
        }
    }
}
