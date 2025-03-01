<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProductsController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Products');
        $this->load->library('form_validation');
        header('Content-Type: application/json');
    }

    // GET /api/products (Menampilkan semua produk)
    public function index()
    {
        // Ambil query parameter dengan nilai default
        $page = (int) $this->input->get('page', TRUE) ?: 1;
        $per_page = (int) $this->input->get('per_page', TRUE) ?: 10;

        // Validasi input
        if ($page < 1 || $per_page < 1) {
            http_response_code(400);
            echo json_encode([
                "message" => "Invalid query parameters",
                "code" => 400,
                "data" => []
            ]);
            return;
        }

        // Hitung offset
        $offset = ($page - 1) * $per_page;

        // Ambil data produk dengan Query Builder CI3
        $products = $this->Products->get_products_pagination($per_page, $offset);
        $total_products = $this->Products->count_all_products(); // Hitung total produk

        if (empty($products)) {
            http_response_code(404);
            echo json_encode([
                "message" => "No data found",
                "code" => 404,
                "data" => []
            ]);
            return;
        }

        // Hitung total halaman
        $total_pages = ceil($total_products / $per_page);

        // Kirim response dengan pagination info
        http_response_code(200);
        echo json_encode([
            "message" => "Success",
            "code" => 200,
            "data" => $products,
            "pagination" => [
                "current_page" => $page,
                "per_page" => $per_page,
                "total_pages" => $total_pages,
                "total_products" => $total_products
            ]
        ]);
    }


    // GET /api/products/{id} (Menampilkan produk berdasarkan ID)
    public function show($id)
    {
        $product = $this->Products->get_product_by_id($id);
        if ($product == null) {
            http_response_code(404);
            echo json_encode(["message" => "Produk tidak ditemukan"]);
        } else {

            http_response_code(200);
            echo json_encode([
                "message" => "Success",
                "code" => 200,
                "data" => $product,
            ]);
        }
    }

    // POST /api/products (Menambahkan produk baru)
    public function store()
    {
        // $input = json_decode(file_get_contents("php://input"), true);

        $this->form_validation->set_rules('name', 'Nama Produk', 'required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('price', 'Harga Produk', 'required|numeric|greater_than[0]');

        // Jika validasi gagal, kirim response error 400
        if ($this->form_validation->run() == FALSE) {
            http_response_code(400);
            echo json_encode([
                "message" => "Validation Failed",
                "code" => 400,
                "errors" => $this->form_validation->error_array()
            ]);
            return;
        }

        // Data yang akan disimpan
        $data = [
            'name'  => $this->input->post('name'),
            'price' => $this->input->post('price'),
        ];

        // Simpan produk ke database
        $store = $this->Products->insert_product($data);
        if ($store) {
            http_response_code(201);
            echo json_encode([
                "message" => "Produk berhasil ditambahkan",
                "code" => 201,
                'data' => $store
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "message" => "Gagal menambahkan produk",
                "code" => 500
            ]);
        }
    }

    // PUT /api/products/{id} (Mengupdate produk berdasarkan ID)
    public function update($id)
    {
        // Tangkap data JSON dari request body
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode([
                "message" => "Invalid input",
                "code" => 400
            ]);
            return;
        }

        // Cek apakah produk ada
        $product = $this->Products->get_product_by_id($id);
        if (!$product) {
            http_response_code(404);
            echo json_encode([
                "message" => "Produk tidak ditemukan",
                "code" => 404
            ]);
            return;
        }

        // Set data untuk validasi
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('name', 'Nama Produk', 'required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('price', 'Harga Produk', 'required|numeric|greater_than[0]');

        if ($this->form_validation->run() == FALSE) {
            http_response_code(400);
            echo json_encode([
                "message" => "Validation Failed",
                "code" => 400,
                "errors" => $this->form_validation->error_array()
            ]);
            return;
        }

        // Data yang akan diperbarui
        $data = [
            'name'  => $input['name'],
            'price' => $input['price'],
        ];

        if ($this->Products->update_product($id, $data)) {
            http_response_code(200);
            echo json_encode([
                "message" => "Produk berhasil diperbarui",
                "code" => 200,
                "data" => $data
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "message" => "Gagal memperbarui produk",
                "code" => 500
            ]);
        }
    }


    // DELETE /api/products/{id} (Menghapus produk berdasarkan ID)
    public function delete($id)
    {
        // Cek apakah produk dengan ID tersebut ada
        $product = $this->Products->get_product_by_id($id);

        if (!$product) {
            http_response_code(404);
            echo json_encode([
                "message" => "Produk tidak ditemukan",
                "code" => 404
            ]);
            return;
        }

        // Jika produk ditemukan, lakukan penghapusan
        if ($this->Products->delete_product($id)) {
            http_response_code(200);
            echo json_encode([
                "message" => "Produk berhasil dihapus",
                "code" => 200,
                "deleted_data" => $product // Menampilkan data produk yang dihapus
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "message" => "Gagal menghapus produk",
                "code" => 500
            ]);
        }
    }
}
