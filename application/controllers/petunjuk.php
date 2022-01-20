<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Petunjuk extends MY_Controller
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
            'bookmark.user_id' => $user_id,
            'bookmark.is_siswa' => $is_siswa
        ]);
        $this->twig->display('petunjuk.html', $data);
    }

    public function materi()
    {
        $this->twig->display('petunjuk-materi.html');
    }

    public function masukkantugas()
    {
        $this->twig->display('petunjuk-masukkantugas.html');
    }

    public function menyimpanbookmark()
    {
        $this->twig->display('petunjuk-menyimpanbookmark.html');
    }

    public function logout()
    {
        $this->twig->display('petunjuk-logout.html');
    }

}
