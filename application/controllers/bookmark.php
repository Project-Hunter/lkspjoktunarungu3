<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bookmark extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        must_login();
    }

    function index()
    {
        if (!empty($_SESSION['login_' . APP_PREFIX]['siswa'])) {
            $user_id = $_SESSION['login_' . APP_PREFIX]['siswa']['user']['id'];
            $is_siswa = 1;
        }elseif (!empty($_SESSION['login_' . APP_PREFIX]['pengajar'])) {
            $user_id = $_SESSION['login_' . APP_PREFIX]['pengajar']['user']['id'];
            $is_siswa = 0;
        }else{
            $user_id = $_SESSION['login_' . APP_PREFIX]['admin']['user']['id'];
            $is_siswa = 0;
        }
        $data['bookmark'] = $this->bookmark_model->retrieve_all([
            'user_id' => $user_id,
            'is_siswa' => $is_siswa
        ]);
        $this->twig->display('list-bookmark.html', $data);
    }

    public function tooglebookmark($materi_id)
    {
        if (!empty($_SESSION['login_' . APP_PREFIX]['siswa'])) {
            $user_id = $_SESSION['login_' . APP_PREFIX]['siswa']['user']['id'];
            $is_siswa = 1;
        }elseif (!empty($_SESSION['login_' . APP_PREFIX]['pengajar'])) {
            $user_id = $_SESSION['login_' . APP_PREFIX]['pengajar']['user']['id'];
            $is_siswa = 0;
        }else{
            $user_id = $_SESSION['login_' . APP_PREFIX]['admin']['user']['id'];
            $is_siswa = 0;
        }
        
        $this->bookmark_model->toogle($materi_id, $user_id, $is_siswa);

        $this->session->set_flashdata('msg', get_alert('success', 'Kompetensi berhasil dihapus.'));

        redirect('materi/detail/'.$materi_id);
    }

    public function tooglebookmark2($materi_id)
    {
        if (!empty($_SESSION['login_' . APP_PREFIX]['siswa'])) {
            $user_id = $_SESSION['login_' . APP_PREFIX]['siswa']['user']['id'];
            $is_siswa = 1;
        }elseif (!empty($_SESSION['login_' . APP_PREFIX]['pengajar'])) {
            $user_id = $_SESSION['login_' . APP_PREFIX]['pengajar']['user']['id'];
            $is_siswa = 0;
        }else{
            $user_id = $_SESSION['login_' . APP_PREFIX]['admin']['user']['id'];
            $is_siswa = 0;
        }
        
        $this->bookmark_model->toogle($materi_id, $user_id, $is_siswa);

        $this->session->set_flashdata('msg', get_alert('success', 'Kompetensi berhasil dihapus.'));

        redirect('bookmark');
    }


}
