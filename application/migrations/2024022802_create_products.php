<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_products extends CI_Migration
{

    public function up()
    {
        // Pilih database 'test_db'
        $this->db->query('USE test_db');

        // Definisi tabel 'products'
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => FALSE,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => FALSE,
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ]);

        // Set primary key
        $this->dbforge->add_key('id', TRUE);

        // Buat tabel 'products'
        $this->dbforge->create_table('products');

        echo "Tabel 'products' berhasil dibuat!";
    }

    public function down()
    {
        // Hapus tabel jika rollback
        $this->dbforge->drop_table('products');

        echo "Tabel 'products' berhasil dihapus!";
    }
}
