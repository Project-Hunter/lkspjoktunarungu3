<?php

/**
 * Class Model untuk resource mapel
 *
 * @package Elearning Dokumenary
 * @link    
 */
class Kompetensidasar_model extends CI_Model
{
    public function create($kelas_id, $mapel_id, $isi, $nomor, $level)
    {
        $kelas_id = (int)$kelas_id;
        $mapel_id = (int)$mapel_id;

        $data = array(
            'kelas_id' => $kelas_id,
            'mapel_id' => $mapel_id,
            'isi' => $isi,
            'nomor' => $nomor,
            'level' => $level,
            'parent_node' => 0,
        );
        $this->db->insert('kompetensi_dasar', $data);
        return $this->db->insert_id();
    }
    
    public function retrieve_all()
    {
        $result = $this->db->get_where('kompetensi_dasar',['level'=> 1]);
        return $result->result_array();
    }

    public function retrieve_page($no_of_records = 10,$page_no = 1)
    {
        $no_of_records = (int)$no_of_records;
        $page_no = (int)$page_no;
        
        $orderby = array('id' => 'DESC');
        $data = $this->pager->set('kompetensi_dasar', $no_of_records, $page_no, [], $orderby);
        return $data;
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
        $isi,
        $nomor,
        $level
    ) {

        $data = array(
            'kelas_id' => $kelas_id,
            'mapel_id' => $mapel_id,
            'isi' => $isi,
            'nomor' => $nomor,
            'level' => $level,
        );
        $this->db->where('id', $id);
        $this->db->update('kompetensi_dasar', $data);
        return true;
    }

    public function retrieve_all_sub($mapel_id,$kelas_id)
    {
        $result = $this->db->get_where('kompetensi_dasar',['mapel_id'=> $mapel_id, 'kelas_id'=> $kelas_id]);
        return $result->result_array();
    }

    public function retrieve_all_sub_wth_node($mapel_id,$kelas_id)
    {

        $this->db->where('mapel_id', $mapel_id);
        $this->db->where('kelas_id', $kelas_id);
        $this->db->where('parent != ', 'NULL');
        $this->db->where('parent_node > ', '0');
        $result = $this->db->get('kompetensi_dasar');
        $result = $result->result_array();

        return $result;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->or_where('parent_node', $id);
        $this->db->delete('kompetensi_dasar');
        return true;
    }

    public function delete_sub($id)
    {
        $this->db->where('id', $id);
        $this->db->or_where('parent', $id);
        $this->db->delete('kompetensi_dasar');
        return true;
    }

    public function create_sub(
        $kelas_id, 
        $mapel_id, 
        $isi, 
        $nomor, 
        $parent_node, 
        $parent, 
        $level
        )
    {
        $kelas_id = (int)$kelas_id;
        $mapel_id = (int)$mapel_id;

        $data = array(
            'kelas_id' => $kelas_id,
            'mapel_id' => $mapel_id,
            'isi' => $isi,
            'nomor' => $nomor,
            'level' => $level,
            'parent_node' => $parent_node,
            'parent' => $parent == '' ? null : $parent,
        );
        $this->db->insert('kompetensi_dasar', $data);
        return $this->db->insert_id();
    }

    public function edit_sub(
        $id,
        $isi, 
        $nomor, 
        $parent, 
        $level
        )
    {

        $data = array(
            'isi' => $isi,
            'nomor' => $nomor,
            'level' => $level,
            'parent' => $parent == '' ? null : $parent,
        );

        $this->db->where('id', $id);
        $this->db->update('kompetensi_dasar', $data);
        return true;
    }

}
