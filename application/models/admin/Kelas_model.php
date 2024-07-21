<?php 



class Kelas_model extends CI_Model{

    public function queryById($data = [])
    {
        $this->db->select('*');
        $this->db->from('kelas');
        $this->db->where('id_kelas',$data['id_kelas']);
        $this->db->join('jurusan', 'kelas.id_jurusan = jurusan.id_jurusan', 'left');
        $query = $this->db->get();
       return $query->row_array();
    }


    public function buatQuery($tabel,$select_column = [],$order_column = [])
    {
        $tabel = strtolower($tabel);

        $this->db->select($select_column);
        $this->db->from($tabel);
        $this->db->join('jurusan', 'kelas.id_jurusan = jurusan.id_jurusan', 'left');
        if(isset($_POST['search']['value']))
        {
            $this->db->like('jurusan.jurusan',$_POST['search']['value']);
        }

        if(isset($_POST['order']))
        {
            $this->db->order_by($order_column[$_POST['order']['0']['column']],$_POST['order']['0']['dir']);
        }else{
            $this->db->order_by('jurusan.jurusan', 'DESC');
        }        
        
    }

    public function buatDataTables($tabel,$select_column=[],$order_column=[]){
        $this->buatQuery($tabel,$select_column,$order_column);
        if($_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function getFilteredData($tabel,$select_column=[],$order_column=[]){
        $this->buatQuery($tabel,$select_column,$order_column);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getAllData($tabel){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->join('jurusan', 'kelas.id_jurusan = jurusan.id_jurusan', 'left');
        return $this->db->count_all_results();
    }

    public function queryCountModular($tabel)
    {
       return $this->db->get($tabel)->num_rows();
       
    }

    public function cekKodeModular($tabel,$kol_id)
    {
        $this->db->select_max($kol_id);
        $query = $this->db->get($tabel); 
       return $query->row_array();
    }

    public function cekId(){
        $tabel = 'kelas';
    
      //id otomatis     
        $kd_id = 'KLS';
        $kol_id = 'id_kelas';
     
    
        
        $jumlah_baris = $this->queryCountModular($tabel);        
        
        //jika jumlah baris = 0
        if($jumlah_baris == 0){
            $kodeIDSekarang = $kd_id.'00001';
        }else{
            //cek kode id terakhir
            $hasil = $this->cekKodeModular($tabel,$kol_id);        
            $hasilKode = $hasil[$kol_id];
            
            $urutan = (int) substr($hasilKode, 3, 5);
    
            // bilangan yang diambil ini ditambah 1 untuk menentukan nomor urut berikutnya
            $urutan++;

            // membentuk kode barang baru
            // perintah sprintf("%03s", $urutan); berguna untuk membuat string menjadi 3 karakter
            // misalnya perintah sprintf("%03s", 15); maka akan menghasilkan '015'
            // angka yang diambil tadi digabungkan dengan kode huruf yang kita inginkan, misalnya BRG 
            
            $kodeIDSekarang = $kd_id . sprintf("%05s", $urutan);        
            
        }

        return $kodeIDSekarang;
    }

    public function getKelas(){
        $this->db->select('*');
        $this->db->from('kelas');
        $this->db->join('jurusan', 'kelas.id_jurusan = jurusan.id_jurusan', 'left');
        return $this->db->get()->result_array();
    }

    public function tambah($data = [],$tabel)
    {

        $kodeIDSekarang = $this->cekId();
    
    
        $data['id_kelas'] = $kodeIDSekarang;
        
    
          //insert data ke tabel kelas
          

         $this->db->insert($tabel,$data);
         $status = $this->db->affected_rows();


      if ($status > 0) {
            return 1;
        } else {
            return 0;
        }
       
    }

    public function ubah($data = [],$tabel)
    {
       
      $tabel = strtolower($tabel); 
     
        $this->db->where('id_kelas', $data['id_kelas']);
        $this->db->update($tabel, $data);
        $status = $this->db->affected_rows();        

        return $status;
       
    }

    public function hapus($data = []){
        $this->db->delete('kelas', $data);
        $status = $this->db->affected_rows();
        if ($status > 0) {
            return 1;
         } else {
            return 0;
         }
    }

}