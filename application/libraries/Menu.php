<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class menu aplikasi e-learning dokumenary.net
 *
 * @author Almazari <almazary@gmail.com>
 * @since  1.8
 */
class Menu
{
    private $menus = array();

    function __construct()
    {
        $this->default_menu();
    }

    private function default_menu()
    {
        $this->menus['admin'] = array(
            0 => array(
                '<a href="' . site_url() . '"><i class="menu-icon icon-home"></i>Beranda</a>',
            ),
            1 => array(
                '<a href="' . site_url('siswa'). '"><i class="menu-icon icon-group"></i>Siswa <span class="menu-count-pending-siswa"></span></a>',
                '<a href="' . site_url('pengajar'). '"><i class="menu-icon icon-user"></i>Pengajar <span class="menu-count-pending-pengajar"></span></a>'
            ),
            2 => array(
                '<a href="' . site_url('tugas?clear_filter=true') . '"><i class="menu-icon icon-tasks"></i>Tugas </a>',
                '<a href="' . site_url('materi?clear_filter=true') . '"><i class="menu-icon icon-book"></i>Materi </a>',
                '<a href="' . site_url('kompetensidasar') . '"><i class="menu-icon icon-book"></i>Kompetensi Dasar </a>',
                '<a href="' . site_url('kompetensiinti') . '"><i class="menu-icon icon-book"></i>Kompetensi Inti </a>',
            ),
            3 => array(
                '<a href="' . site_url('kelas/mapel_kelas') . '"><i class="menu-icon icon-paste"></i>Matapelajaran Kelas </a>',
                '<a href="' . site_url('kelas') . '"><i class="menu-icon icon-tasks"></i>Manajemen Kelas </a>',
                '<a href="' . site_url('mapel') . '"><i class="menu-icon icon-book"></i>Manajemen Matapelajaran </a>'
            ),
            4 => array(
                '<a href="' . site_url('welcome/pengaturan') . '"><i class="menu-icon icon-wrench"></i>Pengaturan</a>',
                '<a href="' . site_url('welcome/hapus_data') . '"><i class="menu-icon icon-trash"></i>Hapus Data</a>',
            ),
            5 => array(
                '<a href="' . site_url('login/logout') . '"><i class="menu-icon icon-signout"></i>Logout </a>'
            )
        );

        $this->menus['pengajar'] = array(
            0 => array(
                '<a href="' . site_url() . '"><i class="menu-icon icon-home"></i>Beranda</a>',
             ),
            1 => array(
                '<a href="' . site_url('tugas?clear_filter=true') . '"><i class="menu-icon icon-tasks"></i>Tugas </a>',
                '<a href="' . site_url('materi?clear_filter=true') . '"><i class="menu-icon icon-book"></i>Materi </a>',
                '<a href="' . site_url('materi/komentar') . '"><i class="menu-icon icon-comments"></i>Komentar saya</a>'
            ),
            2 => array(
                '<a href="' . site_url('pengajar/filter') . '"><i class="menu-icon icon-search"></i>Filter Pengajar </a>',
                '<a href="' . site_url('siswa/filter') . '"><i class="menu-icon icon-search"></i>Filter Siswa </a>'
            ),
            3 => array(
                '<a href="' . site_url('login/logout') . '"><i class="menu-icon icon-signout"></i>Logout </a>'
            )
        );

        $this->menus['siswa'] = array(
            0 => array(
                '<a href="' . site_url() . '"><i class="menu-icon icon-home"></i>Beranda</a>',
            ),
            1 => array(
                '<a href="' . site_url('tugas?clear_filter=true') . '"><i class="menu-icon icon-tasks"></i>Tugas </a>',
                '<a href="' . site_url('materi?clear_filter=true') . '"><i class="menu-icon icon-book"></i>Materi </a>',
            ),
            2 => array(
                '<a href="' . site_url('login/logout') . '"><i class="menu-icon icon-signout"></i>Logout </a>'
            )
        );
    }

    public function add($rule, $index, $link)
    {
        $this->menus[$rule][$index][] = $link;
    }

    public function get()
    {
        if (is_admin()) {
            return $this->menus['admin'];
        } elseif (is_pengajar()) {
            return $this->menus['pengajar'];
        } elseif (is_siswa()) {
            return $this->menus['siswa'];
        }
    }
}
