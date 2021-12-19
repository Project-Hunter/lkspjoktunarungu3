<?php

/**
 * Class Model untuk resource mapel
 *
 * @package Elearning Dokumenary
 * @link    
 */
class Bookmark_model extends CI_Model
{
    public function retrieve_all($where)
    {
        $this->db->select('*');
        $this->db->from('bookmark');
        $this->db->join('materi', 'materi.id = bookmark.materi_id');
        $this->db->where($where);
        $result = $this->db->get();
        return $result->result_array();
    }

    public function toogle($materi_id, $user_id, $is_siswa)
    {
        $data = array(
            'materi_id' => $materi_id,
            'user_id' => $user_id,
            'is_siswa' => $is_siswa
        );
        $check = $this->retrieve($data);

        if (!empty($check)) {
            $this->delete($data);
        }else{
            $this->insert($data);
        }
        return true;
    }

    public function retrieve($where)
    {
        $this->db->where($where);
        $result = $this->db->get('bookmark');
        return $result->row_array();
    }

    public function delete($where)
    {
        $this->db->where($where);
        $this->db->delete('bookmark');
        return true;
    }

    public function insert($data)
    {
        $this->db->insert('bookmark', $data);
        return $this->db->insert_id();
    }
    
}
