<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class KompetensiDasar extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        must_login();

        # harus login sebagai admin
        if (!is_admin()) {
            redirect('welcome');
        }
    }

    private function kelas_hirarki(&$str_kelas = "", $parent_id = null, $order = 0){
        $kelas = $this->kelas_model->retrieve_all($parent_id);
        if(count($kelas) > 0){
            if(is_null($parent_id)){
                $str_kelas .= '<ol class="sortable" id="kelas">';
            }else{
                $str_kelas .= '<ol>';
            }
        }

        foreach ($kelas as $m){
            $order++;
            $str_kelas .= '<li id="list_'.$m['id'].'">
            <div>
                <span class="disclose" id="kelas"><span>
                </span></span>
                <span class="pull-right">
                    <a href="'.site_url('kelas/edit/'.$m['id']).'" title="Edit"><i class="icon icon-edit"></i>Edit</a>
                </span>';
                if ($m['aktif'] == 1) {
                    $str_kelas .= '<b>'.$m['nama'].'</b>';
                } else {
                    $str_kelas .= '<b class="text-muted">'.$m['nama'].'</b>';
                }
            $str_kelas .= '</div>';

                $this->kelas_hirarki($str_kelas, $m['id'], $order);
            $str_kelas .= '</li>';
        }

        if(count($kelas) > 0){
            $str_kelas .= '</ol>';
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
            $this->kompetensidasar_model->create($kelas_id, $mapel_id, $isi, $nomor);
            
            $this->session->set_flashdata('kompetensidasar', get_alert('success', 'Kompetensi Dasar baru berhasil disimpan.'));
            redirect('kompetensidasar/index');
        }

        $data['mapel'] = $this->mapel_model->retrieve_all_mapel();
        $data['kelas'] = $this->kelas_model->retrieve_all();

        $this->twig->display('tambah-kompetensi-dasar.html',$data);
    }

    // private function hirarki(){
    //     $parent = $this->kompetensidasar_model->retrieve_all();

    //     $return = '';
    //     foreach ($parent as $p) {
    //         $return .= '<div class="parent-kelas" id="parent-'.$p['id'].'">'.$p['isi'].'</div>';


    //     }

    //     return $return;
    // }



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
        // print_r($id);die();
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

    private function mapel_kelas_hirarki($view = '', $params = array()){
        $parent = $this->kelas_model->retrieve_all(null, !empty($params['parent_id']) ? array('id' => $params['parent_id']) : array());

        $return = '';
        foreach ($parent as $p) {
            $return .= '<div class="parent-kelas" id="parent-'.$p['id'].'">'.$p['nama'].'</div>';

            $sub_kelas = $this->kelas_model->retrieve_all($p['id'], !empty($params['sub_id']) ? array('id' => $params['sub_id']) : array());
            foreach ($sub_kelas as $s) {
                $return .= '<div class="panel panel-default" id="subkelas-'.$s['id'].'" style="margin-left:25px;margin-bottom:5px;">';

                switch ($view) {
                    default:
                        $return .= '<div class="panel-heading">
                            '.$s['nama'].'&nbsp;&nbsp;'.(($s['aktif'] == 0) ? '<span class="label label-warning">Kelas tidak aktif</span>' : '').'
                            '.(($s['aktif'] == 1) ? '<a href="'.site_url('kelas/mapel_kelas/add/'.$p['id'].'/'.$s['id'].'/'.enurl_redirect(current_url())).'" class="btn btn-primary pull-right" style="margin-top:-5px;"><i class="icon-wrench"></i> Atur Matapelajaran</a>' : '').'
                        </div>';
                        if ($s['aktif'] == 1) {
                            $return .= '<div class="panel-body">';

                            $return .= get_flashdata('edit-mapel-kelas-'.$s['id']);

                            $retrieve_all = $this->mapel_model->retrieve_all_kelas(null, $s['id']);
                            $return .= '<table class="table table-striped table-condensed">
                            <tbody>';
                                foreach ($retrieve_all as $v):
                                $m = $this->mapel_model->retrieve($v['mapel_id']);
                                if (empty($m)) {
                                    continue;
                                }
                                $return .= '<tr>
                                    <td style="border-top:0px;">
                                        <div class="btn-group pull-right">';
                                            if ($v['aktif'] == 0) {
                                                $return .= '<a class="btn btn-success btn-small" href="'.site_url('kelas/mapel_kelas/aktifkan/'.$p['id'].'/'.$s['id'].'/'.$v['id'].'/'.enurl_redirect(current_url())).'"><i class="icon-ok"></i> Aktifkan</a>';
                                            } else {
                                                $return .= '<a class="btn btn-danger btn-small" href="#modal-'.$v['id'].'" data-toggle="modal"><i class="icon-trash"></i> Hapus</a>';
                                            }
                                        $return .= '</div>
                                        <b>
                                        '.$m['nama'].'
                                        '.(($v['aktif'] == 0) ? '<span class="text-error"><i class="icon-info-sign"></i> Matapelajaran Kelas tidak aktif' : '').'
                                        </b>

                                        <div id="modal-'.$v['id'].'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-header">
                                                <h3 id="myModalLabel">Anda yakin ingin menghapus Matapelajaran Kelas ini?</h3>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Batal</button>
                                                <a class="btn btn-danger" href="'.site_url('kelas/mapel_kelas/remove/'.$p['id'].'/'.$s['id'].'/'.$v['id'].'/'.enurl_redirect(current_url())).'">Tetap Hapus</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>';
                                endforeach;
                            $return .= '</tbody>
                            </table>';
                            $return .= '</div>';
                        }
                        break;
                }

                $return .= '</div>';
            }

        }

        return $return;
    }

    function mapel_kelas($act = 'list', $segment_4 = '', $segment_5 = '', $segment_6 = '', $segment_7 = '')
    {
        switch ($act) {
            case 'aktifkan':
                $parent_id      = (int)$segment_4;
                $kelas_id       = (int)$segment_5;
                $mapel_kelas_id = (int)$segment_6;
                $uri_back       = (string)$segment_7;

                # ambil parent
                $parent = $this->kelas_model->retrieve($parent_id);
                if (empty($parent)) {
                    redirect('kelas/mapel_kelas');
                }

                $kelas = $this->kelas_model->retrieve($kelas_id);
                if (empty($kelas)) {
                    redirect('kelas/mapel_kelas');
                }

                $mapel_kelas = $this->mapel_model->retrieve_kelas($mapel_kelas_id);
                if (empty($mapel_kelas)) {
                    redirect('kelas/mapel_kelas');
                }

                if (empty($uri_back)) {
                    $uri_back = site_url('kelas/mapel_kelas/#subkelas-'.$kelas_id);
                } else {
                    $uri_back = deurl_redirect($uri_back);
                    $uri_back = rtrim($uri_back, '/');
                    $uri_back = $uri_back.'/#subkelas-'.$kelas_id;
                }

                if (!is_demo_app()) {
                    # update
                    $this->mapel_model->update_kelas($mapel_kelas_id, $mapel_kelas['kelas_id'], $mapel_kelas['mapel_id'], 1);
                }

                $this->session->set_flashdata('edit-mapel-kelas-'.$kelas['id'], get_alert('success', 'Matapelajaran kelas berhasil diaktifkan.'));
                redirect($uri_back);
            break;

            case 'remove':
                $parent_id      = (int)$segment_4;
                $kelas_id       = (int)$segment_5;
                $mapel_kelas_id = (int)$segment_6;
                $uri_back       = (string)$segment_7;

                # ambil parent
                $parent = $this->kelas_model->retrieve($parent_id);
                if (empty($parent)) {
                    redirect('kelas/mapel_kelas');
                }

                $kelas = $this->kelas_model->retrieve($kelas_id);
                if (empty($kelas)) {
                    redirect('kelas/mapel_kelas');
                }

                if (empty($uri_back)) {
                    $uri_back = site_url('kelas/mapel_kelas/#subkelas-'.$kelas_id);
                } else {
                    $uri_back = deurl_redirect($uri_back);
                    $uri_back = rtrim($uri_back, '/');
                    $uri_back = $uri_back.'/#subkelas-'.$kelas_id;
                }

                if (!is_demo_app()) {
                    # hapus data
                    $this->mapel_model->delete_kelas($mapel_kelas_id);
                }

                $this->session->set_flashdata('edit-mapel-kelas-'.$kelas['id'], get_alert('warning', 'Matapelajaran kelas berhasil dihapus.'));
                redirect($uri_back);
            break;

            case 'add':
                $parent_id = (int)$segment_4;
                $kelas_id  = (int)$segment_5;
                $uri_back  = (string)$segment_6;

                # ambil parent
                $parent = $this->kelas_model->retrieve($parent_id);
                if (empty($parent)) {
                    redirect('kelas/mapel_kelas');
                }

                $kelas = $this->kelas_model->retrieve($kelas_id);
                if (empty($kelas)) {
                    redirect('kelas/mapel_kelas');
                }

                if (empty($uri_back)) {
                    $uri_back = site_url('kelas/mapel_kelas/add/'.$parent_id.'/'.$kelas_id);
                } else {
                    $uri_back = deurl_redirect($uri_back);
                }
                $data['uri_back'] = $uri_back;

                $content_file   = 'tambah-mapel-kelas.html';
                $data['kelas']  = $kelas;
                $data['parent'] = $parent;

                # ambil semua matapelajaran
                $retrieve_all   = $this->mapel_model->retrieve_all_mapel();
                $data['mapels'] = $retrieve_all;

                # ambil matapelajaran pada kelas ini
                $retrieve_all_kelas = $this->mapel_model->retrieve_all_kelas();
                $mapel_kelas_id = array();
                foreach ($retrieve_all_kelas as $v) {
                    $mapel_kelas_id[] = $v['mapel_id'];
                }

                $data['post_mapel'] = 0;
                if (!empty($_POST['add-mapel'])) {
                    if ($this->form_validation->run('mapel/add') == TRUE AND !is_demo_app()) {
                        $nama = $this->input->post('nama', TRUE);
                        $info = $this->input->post('info', TRUE);
                        $this->mapel_model->create($nama, $info);

                        $this->session->set_flashdata('mapel', get_alert('success', 'Matapelajaran baru berhasil ditambah.'));
                        redirect('kelas/mapel_kelas/add/'.$parent_id.'/'.$kelas_id.'/'.enurl_redirect($uri_back));
                    }
                    $data['post_mapel'] = 1;
                }

                if ($this->form_validation->run('kelas/mapel_kelas/add') == TRUE AND !is_demo_app()) {

                    $mapel = $this->input->post('mapel', TRUE);

                    $mapel_post_id = array();
                    foreach ($mapel as $mapel_id) {
                        if (is_numeric($mapel_id) AND $mapel_id > 0) {
                            # cek dulu
                            $check = $this->mapel_model->retrieve_kelas(null, $kelas_id, $mapel_id);
                            if (empty($check)) {
                                $this->mapel_model->create_kelas($kelas_id, $mapel_id);
                            } else {
                                # update aktif jadi 1
                                $this->mapel_model->update_kelas($check['id'], $kelas_id, $mapel_id, 1);
                            }
                            $mapel_post_id[] = $mapel_id;
                        }
                    }

                    # cari perbedaan
                    if (count($mapel_kelas_id) > count($mapel_post_id)) {
                        $diff_mapel_kelas = array_diff($mapel_kelas_id, $mapel_post_id);
                        foreach ($diff_mapel_kelas as $mapel_id) {
                            # ambil data
                            $retrieve = $this->mapel_model->retrieve_kelas(null, $kelas_id, $mapel_id);

                            if (!empty($retrieve)) {
                                # hapus
                                $this->mapel_model->delete_kelas($retrieve['id']);
                            }
                        }
                    }

                    $this->session->set_flashdata('mapel', get_alert('success', 'Matapelajaran kelas berhasil disimpan.'));
                    redirect('kelas/mapel_kelas/add/'.$parent_id.'/'.$kelas_id.'/'.enurl_redirect($uri_back));
                }
            break;

            default:
            case 'list':
                # detect post
                if (!empty($_POST)) {
                    $post_parent_id = (int)$this->input->post('parent_kelas', true);
                    $post_sub_id    = (int)$this->input->post('sub_kelas', true);
                    redirect('kelas/mapel_kelas/list/'.$post_parent_id.'/'.$post_sub_id);
                }

                $content_file    = 'list-mapel-kelas.html';
                $parent_kelas_id = (int)$segment_4;
                $sub_kelas_id    = (int)$segment_5;

                $data['filter']['parent_id'] = $parent_kelas_id;
                $data['filter']['sub_id']    = $sub_kelas_id;

                if (!empty($parent_kelas_id)) {
                    $data['filter']['result']['parent'] = $this->kelas_model->retrieve($parent_kelas_id);
                    $data['sub_kelas'] = $this->kelas_model->retrieve_all($parent_kelas_id, array('aktif' => 1));
                }

                if (!empty($sub_kelas_id)) {
                    $data['filter']['result']['sub'] = $this->kelas_model->retrieve($sub_kelas_id);
                }

                $data['mapel_kelas_hirarki'] = $this->mapel_kelas_hirarki('', array('parent_id' => $parent_kelas_id, 'sub_id' => $sub_kelas_id));
                $data['parent_kelas']        = $this->kelas_model->retrieve_all(null, array('aktif' => 1));

            break;
        }

        $this->twig->display($content_file, $data);
    }
}
