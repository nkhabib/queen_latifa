<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Products extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->library('form_validation');
        header('Content-Type: application/json');
    }

    // GET /api/products (Menampilkan semua produk)
    public function index()
    {
        $products = $this->Product_model->get_products();
        echo json_encode($products);
    }

    // GET /api/products/{id} (Menampilkan produk berdasarkan ID)
    public function show($id)
    {
        $product = $this->Product_model->get_products($id);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Produk tidak ditemukan"]);
        }
    }

    // POST /api/products (Menambahkan produk baru)
    public function store()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['name']) || !isset($input['price'])) {
            http_response_code(400);
            echo json_encode(["message" => "Data tidak lengkap"]);
            return;
        }

        $data = [
            'name'  => $input['name'],
            'price' => $input['price']
        ];

        if ($this->Product_model->insert_product($data)) {
            http_response_code(201);
            echo json_encode(["message" => "Produk berhasil ditambahkan"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Gagal menambahkan produk"]);
        }
    }

    // PUT /api/products/{id} (Mengupdate produk berdasarkan ID)
    public function update($id)
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['name']) || !isset($input['price'])) {
            http_response_code(400);
            echo json_encode(["message" => "Data tidak lengkap"]);
            return;
        }

        $data = [
            'name'  => $input['name'],
            'price' => $input['price']
        ];

        if ($this->Product_model->update_product($id, $data)) {
            echo json_encode(["message" => "Produk berhasil diperbarui"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Gagal memperbarui produk"]);
        }
    }

    // DELETE /api/products/{id} (Menghapus produk berdasarkan ID)
    public function delete($id)
    {
        if ($this->Product_model->delete_product($id)) {
            echo json_encode(["message" => "Produk berhasil dihapus"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Gagal menghapus produk"]);
        }
    }
}
