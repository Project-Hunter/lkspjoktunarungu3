<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class KompetensiDasar extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        must_login();

        if (!is_pengajar_or_is_admin()) {
            redirect('welcome');
        }
    }

    function index($segment_3 = '')
    {
        $str_kelas = '';
        $data['kelas_hirarki'] = $str_kelas;
        $data['mapel'] = $this->mapel_model->retrieve_all_mapel();
        $data['kelas'] = $this->kelas_model->retrieve_all();
        $data['kompetensidasar'] = $this->kompetensidasar_model->retrieve_all();
        // $data['kompetensidasar_hirarki'] = $this->hirarki();

        $page_no = (int)$segment_3;
        if (empty($page_no)) {
            $page_no = 1;
        }

        $retrieve_page = $this->kompetensidasar_model->retrieve_page(1,$page_no);
        // $data['kompetensidasar'] = $retrieve_page['result'];
        $data['pagination'] = $this->pager->view($retrieve_page, 'kompetensidasar/index/');
        // echo "<pre>";print_r($data);die();
        $this->twig->display('list-kompetensi-dasar.html', $data);
    }

    function add()
    {
        if ($this->form_validation->run('kompetensidasar/add') == TRUE AND !is_demo_app()) {
            $kelas_id = $this->input->post('kelas_id', TRUE);
            $mapel_id = $this->input->post('mapel_id', TRUE);
            $isi = $this->input->post('isi', TRUE);
            $nomor = $this->input->post('nomor', TRUE);
            $this->kompetensidasar_model->create($kelas_id, $mapel_id, $isi, $nomor, 1);
            
            $this->session->set_flashdata('kompetensidasar', get_alert('success', 'Kompetensi Dasar baru berhasil disimpan.'));
            redirect('kompetensidasar/index');
        }

        $data['mapel'] = $this->mapel_model->retrieve_all_mapel();
        $data['kelas'] = $this->kelas_model->retrieve_all();

        $this->twig->display('tambah-kompetensi-dasar.html',$data);
    }


    function edit($segment_3 = '',$segment_4 = '')
    {

        $id = (int)$segment_3;
        $uri_back = (string)$segment_4;

        if (empty($uri_back)) {
            $uri_back = site_url('kompetensidasar');
        } else {
            $uri_back = deurl_redirect($uri_back);
        }
        $data['uri_back'] = $uri_back;
        $data['id'] = $id;

        $data['mapel'] = $this->mapel_model->retrieve_all_mapel();
        $data['kelas'] = $this->kelas_model->retrieve_all();
        $kd    = $this->kompetensidasar_model->retrieve($id);

        $data['kompetensidasar'] = $kd;

        # post action
        $success = false;
        if ($this->form_validation->run('kompetensidasar/edit') == TRUE) {
            $mapel_id = $this->input->post('mapel_id', TRUE);
            $kelas_id = $this->input->post('kelas_id', TRUE);
            $isi    = $this->input->post('isi', TRUE);
            $nomor    = $this->input->post('nomor', TRUE);

            $this->kompetensidasar_model->update(
                $id,
                $kelas_id,
                $mapel_id,
                $isi,
                $nomor
            );

            $success = true;
        }

        if ($success) {
            $this->session->set_flashdata('kompetensidasar', get_alert('success', 'Kompetensi Dasar berhasil diperbaharui.'));
            redirect($uri_back);
        }

        $this->twig->display('edit-kompetensi-dasar.html', $data);
    }

    public function delete($id)
    {
        $this->kompetensidasar_model->delete($id);

        $this->session->set_flashdata('msg', get_alert('success', 'Kompetensi berhasil dihapus.'));

        redirect('kompetensidasar');
    }

    public function delete_sub($id)
    {
        $kd = $this->kompetensidasar_model->retrieve($id);
        $parent_node = $kd['parent_node'];

        $this->kompetensidasar_model->delete_sub($id);

        $this->session->set_flashdata('msg', get_alert('success', 'Kompetensi berhasil dihapus.'));

        redirect('kompetensidasar/sub/'.$parent_node);
    }

    function sub($segment_3 = '',$segment_4 = '')
    {

        $id = (int)$segment_3;
        $uri_back = (string)$segment_4;

        if (empty($uri_back)) {
            $uri_back = site_url('kompetensidasar');
        } else {
            $uri_back = deurl_redirect($uri_back);
        }
        $data['uri_back'] = $uri_back;
        $data['id'] = $id;

        $kd = $this->kompetensidasar_model->retrieve($id);

        $kompetensidasar_model = $this->kompetensidasar_model->retrieve_all_sub_wth_node($kd['mapel_id'],$kd['kelas_id']);

        $data['kd'] = $kd;
        $data['kompetensidasar'] = $kompetensidasar_model;

        # post action
        $success = false;
        if ($this->form_validation->run('kompetensidasar/edit') == TRUE) {
            $mapel_id = $this->input->post('mapel_id', TRUE);
            $kelas_id = $this->input->post('kelas_id', TRUE);
            $isi    = $this->input->post('isi', TRUE);
            $nomor    = $this->input->post('nomor', TRUE);

            $this->kompetensidasar_model->update(
                $id,
                $kelas_id,
                $mapel_id,
                $isi,
                $nomor
            );

            $success = true;
        }

        if ($success) {
            $this->session->set_flashdata('kompetensidasar', get_alert('success', 'Kompetensi Dasar berhasil diperbaharui.'));
            redirect($uri_back);
        }

        $this->twig->display('sub-kompetensi-dasar.html', $data);
    }

    function add_sub($id)
    {
        if ($this->form_validation->run('kompetensidasar/add_sub') == TRUE AND !is_demo_app()) {
            $kelas_id = $this->input->post('kelas_id', TRUE);
            $mapel_id = $this->input->post('mapel_id', TRUE);
            $isi = $this->input->post('isi', TRUE);
            $nomor = $this->input->post('nomor', TRUE);
            $parent_node = $this->input->post('parent_node', TRUE);
            $parent = $this->input->post('parent', TRUE);

            $parent = $parent == '' ? $parent_node : $parent;

            $level = 2;

            if(!empty($parent)){
                $check_parent = $this->kompetensidasar_model->retrieve($parent);
                $level = $check_parent['level'] + 1;
            }

            $this->kompetensidasar_model->create_sub($kelas_id,$mapel_id,$isi,$nomor,$parent_node,$parent,$level);
            
            $this->session->set_flashdata('kompetensidasar', get_alert('success', 'Kompetensi Dasar baru berhasil disimpan.'));
            redirect('kompetensidasar/sub/'.$id);
        }

        $kd = $this->kompetensidasar_model->retrieve($id);
        
        $data['id'] = $id;
        $data['kd'] = $kd;
        $data['mapel'] = $this->mapel_model->retrieve_all_mapel();
        $data['kelas'] = $this->kelas_model->retrieve_all();
        $data['parent'] = $this->kompetensidasar_model->retrieve_all_sub_wth_node($kd['mapel_id'], $kd['kelas_id']);

        $this->twig->display('tambah-sub-kompetensi-dasar.html',$data);
    }

    
    function edit_sub($id)
    {
        if ($this->form_validation->run('kompetensidasar/edit_sub') == TRUE) {
            $isi = $this->input->post('isi', TRUE);
            $nomor = $this->input->post('nomor', TRUE);
            $parent_node = $this->input->post('parent_node', TRUE);
            $parent = $this->input->post('parent', TRUE);

            $parent = $parent == '' ? $parent_node : $parent;

            $level = 2;

            if(!empty($parent)){
                $check_parent = $this->kompetensidasar_model->retrieve($parent);
                $level = $check_parent['level'] + 1;
            }

            $this->kompetensidasar_model->edit_sub($id, $isi,$nomor,$parent,$level);
            
            $this->session->set_flashdata('kompetensidasar', get_alert('success', 'Kompetensi Dasar baru berhasil disimpan.'));
            redirect('kompetensidasar/sub/'.$id);
        }

        $kd_this = $this->kompetensidasar_model->retrieve($id);
        $kd = $this->kompetensidasar_model->retrieve($kd_this['parent_node']);

        $data['id'] = $id;
        $data['kd'] = $kd;
        $data['kd_this'] = $kd_this;
        $data['mapel'] = $this->mapel_model->retrieve_all_mapel();
        $data['kelas'] = $this->kelas_model->retrieve_all();
        $data['parent'] = $this->kompetensidasar_model->retrieve_all_sub_wth_node($kd['mapel_id'], $kd['kelas_id']);

        $this->twig->display('edit-sub-kompetensi-dasar.html', $data);
    }

}
