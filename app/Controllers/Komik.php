<?php

namespace App\Controllers;

use App\Models\KomikModel;

class Komik extends BaseController
{
    protected $komikModel;
    public function __construct()
    {
        $this->komikModel = new KomikModel();
    }

    public function index()
    {
        // $komik = $this->komikModel->findAll();
        
        $data = [
            'title' => 'Daftar Komik',
            'komik' => $this->komikModel->getKomik(),
        ];


        return view('komik/index', $data);
    }

    public function detail($slug)
    {
        $data = [
            'title' => 'Detail Komik',
            'komik' => $this->komikModel->getKomik($slug),
        ];

        //jika data tidak ada dalam tabel
        if (empty($data['komik'])){
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul komik ' . $slug . ' tidak ditemukan.');
        }

        return view('komik/detail', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Form tambah komik',
            'validation' => \Config\Services::validation(),
        ];

        return view('komik/create', $data);
    }

    public function save()
    {

        //validasi input
        if (!$this->validate([
            'judul' => ['rules' => 'required|is_unique[komik.judul]',
                        'errors' => [
                            'required' => '{field} belum diisi.',
                            'is_unique' => '{field} tidak boleh sama dengan data sebelumnya.',
                            ]],
            'penulis' => ['rules' => 'required',
                        'errors' => [
                            'required' => '{field} penulis belum diisi.'
                            ]],
            'penerbit' => ['rules' => 'required',
                        'errors' => [
                            'required' => '{field} penerbit belum diisi.'
                            ]],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Gambar tidak boleh lebih dari 1 MB',
                    'is_image' => 'File anda bukan gambar',
                    'mime_in' => 'Gambar harus berekstensi: jpg,jpeg,png',
                ]
            ],
        ])){
            return redirect()->to('/komik/create')->withInput();
        }

        //ambil gambar
        $fileSampul = $this->request->getFile('sampul');
        if ($fileSampul->getError() == 4){
            $namaSampul = 'default.jpg';
        }else{
            $namaSampul = $fileSampul->getRandomName();
            $fileSampul->move('img', $namaSampul);
        }

        //method simpan data
        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul,
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambah.');

        return redirect()->to('/komik');
    }

    //Ubah data
    public function edit($slug)
    {
        $data = [
            'title' => 'Form ubah komik',
            'validation' => \Config\Services::validation(),
            'komik' => $this->komikModel->getKomik($slug),
        ];

        return view('komik/edit', $data);
    }

    public function update($id)
    {
        //cek validasi judul yang sama
        $komikLama = $this->komikModel->getKomik($this->request->getVar('slug'));
        if ($komikLama['judul'] == $this->request->getVar('judul')){
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[komik.judul]';
        }

        //validasi input
        if (!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required' => '{field} belum diisi.',
                    'is_unique' => '{field} tidak boleh sama dengan data sebelumnya.',
                ]
            ],
            'penulis' => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} penulis belum diisi.'
                ]
            ],
            'penerbit' => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} penerbit belum diisi.'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Gambar tidak boleh lebih dari 1 MB',
                    'is_image' => 'File anda bukan gambar',
                    'mime_in' => 'Gambar harus berekstensi: jpg,jpeg,png',
                ]
            ],
        ])) {
            return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput();
        }

        $fileSampul = $this->request->getFile('sampul');
        if ($fileSampul->getError() == 4){
            $namaSampul = $this->request->getVar('sampulLama');
        }else{
            $namaSampul = $fileSampul->getRandomName();
            $fileSampul->move('img', $namaSampul);
            unlink('img/'. $this->request->getVar('sampulLama'));
        }

        //method simpan data
        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul,
        ]);

        session()->setFlashdata('pesan', 'Data berhasil diubah.');

        return redirect()->to('/komik');
    }

    //Hapus data
    public function delete($id)
    {
        $komik = $this->komikModel->find($id);
        if ($komik['sampul'] != 'default.jpg'){
            unlink('img/'. $komik['sampul']);
        }

        $this->komikModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/komik');
    }

}
