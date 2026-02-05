<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CustomFields extends CI_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('CustomField_model', 'custom_field_model');
        $this->table = 'custom_fields';
        
    }

    public function index()
    {
        $data['CustomFields'] = $this->common_model->get_all($this->table);
        $this->load->view('custom_fields/index', $data);
    }

    public function create()
    {
        $data['action'] = 'add';
        $this->load->view('custom_fields/create', $data);
    }

    public function saveCustomField()
    {
        $this->form_validation->set_rules('field_label', 'Field Label', 'required');
        $this->form_validation->set_rules('field_type', 'Field Type', 'required');
        if ($this->form_validation->run() == FALSE) {
            $message = [
                'status'  => 'error',
                'message' => 'Please fill all required fields',
            ];
        } else {
            $fieldData = $this->input->post();
            $data = [
                'field_label' => trim($fieldData['field_label']),
                'field_name'  => url_title($fieldData['field_label'], '_', true),
                'field_type'  => $fieldData['field_type'],
                'is_required' => isset($fieldData['is_required']) ? 1 : 0
            ];
            if($fieldData['action'] == 'edit') {
                $this->common_model->update($this->table, $fieldData['c_id'], $data);
                $message = [
                    'status'  => 'success',
                    'message' => 'Custom field updated succesfully.',
                ];
            } else {
                $this->common_model->insert($this->table, $data);
                $message = [
                    'status'  => 'success',
                    'message' => 'Custom field added succesfully.',
                ];
            }
        }
        echo json_encode($message);
        exit;
    }

    public function edit($id=null)
    {
        if ($id) {
            $data['action'] = 'edit';
            $data['field'] = $this->common_model->get_by_id($this->table, $id);
            $this->load->view('custom_fields/create', $data);
        } else {
            show_404();
        }
    }

    public function delete()
    {
        $fieldId = (int) $this->input->post('id');
        if (!$fieldId) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
            exit;
        }
        $result = $this->custom_field_model->delete_custom_field($fieldId);
        if ($result === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
        } else {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Custom field deleted succesfully.',
            ]);
        }
    }
}
