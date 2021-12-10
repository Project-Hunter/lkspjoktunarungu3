<?php

/**
 * Class Model untuk resource mapel
 *
 * @package Elearning Dokumenary
 * @link    http://www.dokumenary.net
 */
class Kompetensidasar_model extends CI_Model
{
    public function create($kelas_id, $mapel_id, $isi)
    {
        $kelas_id = (int)$kelas_id;
        $mapel_id = (int)$mapel_id;

        $data = array(
            'kelas_id' => $kelas_id,
            'mapel_id' => $mapel_id,
            'isi' => $isi,
        );
        $this->db->insert('kompetensi_dasar', $data);
        return $this->db->insert_id();
    }
    
    public function retrieve_all()
    {
        $result = $this->db->get('kompetensi_dasar');
        return $result->result_array();
    }

    public function retrieve($id)
    {
        $id = (int)$id;

        $this->db->where('id', $id);
        $result = $this->db->get('kompetensi_dasar');
        return $result->row_array();
    }

    public function update(
        $id,
        $kelas_id,
        $mapel_id,
        $isi
    ) {

        $data = array(
            'kelas_id' => $kelas_id,
            'mapel_id' => $mapel_id,
            'isi' => $isi,
        );
        $this->db->where('id', $id);
        $this->db->update('kompetensi_dasar', $data);
        return true;
    }

}
