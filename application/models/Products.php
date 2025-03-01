<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Products extends CI_Model
{

    public function get_products_pagination($limit, $offset)
    {
        return $this->db->select('*')
            ->from('products')
            ->limit($limit, $offset)
            ->get()
            ->result_array();
    }

    // Hitung total jumlah produk
    public function count_all_products()
    {
        return $this->db->from('products')->count_all_results();
    }

    public function get_product_by_id($id)
    {
        return $this->db->get_where('products', ['id' => $id])->row_array();
    }

    public function insert_product($data)
    {
        $this->db->insert('products', $data);
        $insert_id = $this->db->insert_id(); // Ambil ID produk yang baru ditambahkan

        // Ambil kembali data berdasarkan ID
        return $this->db->get_where('products', ['id' => $insert_id])->row_array();
    }

    public function update_product($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('products', $data);
        return $this->db->get_where('products', ['id' => $id])->row_array();
    }

    public function delete_product($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('products');
    }
}
