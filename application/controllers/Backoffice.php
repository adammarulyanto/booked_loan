<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Backoffice extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function index()
	{
 		if($this->session->userdata('status')!="login"){
 			redirect(base_url().'login');
 		}
		$data['booked_loan'] = $this->db->query("select DATE_FORMAT(bold_booked_month,'%M %y') bulan, sum(ifnull(ld_loan_amount,lfd_cap_amount)) amount from booked_loan_data left join loan_data on ld_id = bold_data_id and bold_type=1 left join line_facility_data on lfd_id = bold_data_id and bold_type=2 where year(bold_booked_month)='2021' GROUP BY bulan ORDER BY bold_booked_month")->result();
		$data['product'] = $this->db->query("select mp_product_name product,sum(ifnull(ld_loan_amount,lfd_cap_amount)) amount from booked_loan_data left join loan_data on ld_id = bold_data_id and bold_type=1 left join line_facility_data on lfd_id = bold_data_id and bold_type=2 left join mr_product on mp_id = ifnull(ld_product_id,lfd_product_id) GROUP BY product")->result();
		$data['summary'] = $this->db->query("select 0 user_cnt,0 stok, 0 penjualan, 0 barang_terjual")->result();
		$this->load->view('include/header_bo');
		$this->load->view('include/navbar_bo');
		$this->load->view('home',$data);
		$this->load->view('include/footer_bo');
	}
	public function booking(){
		$prod = $this->uri->segment(3); 
		$get_book = $this->input->post('booked');
		$get_unbook = $this->input->post('unbook');
		$get_ml_number = $this->input->post('ml_number');
		$get_booked_month = $this->input->post('booked_month');
		if(empty($get_book)){
			$book = 0;
			$unbook =$get_unbook;
			$ml_number=$get_ml_number;
			$booked_month=$get_booked_month;
		}else{
			$book = $get_book;
			$unbook =$get_unbook;
			$ml_number=$get_ml_number;
			$booked_month=$get_booked_month;
		}

		echo json_encode($book)."<br>";
		echo json_encode($booked_month)."<br>";
		$count = count($book);
		if($count>1){
			$in_id = implode(',',$book);
			$in_id_unbook = implode(',',$unbook);
		}else{
			$in_id = $book[0];
			$in_id_unbook = $unbook[0];
		}
		if($in_id==0){
			$delete =  $this->db->query("DELETE FROM booked_loan_data where bold_data_id in ($in_id_unbook)");
		}
		if($prod==1){
			for($i=0;$i<$count;$i++){
			$insert =  $this->db->query("INSERT INTO `booked_loan_data` (`bold_data_id`, `bold_type`, `bold_booked_month`) select ld_id,1,ld_disbursed_date from loan_data where ld_id = $book[$i] and ld_id not in (select bold_data_id from booked_loan_data where bold_type=1)");
				if($ml_number[$i]=='NULL' or $ml_number[$i]==''){
					$update_ml_number = $this->db->query("UPDATE loan_data set ld_ml_number = null where ld_id = $book[$i]");
				}else{
					$update_ml_number = $this->db->query("UPDATE loan_data set ld_ml_number = '$ml_number[$i]' where ld_id = $book[$i]");
				}
			}
			$delete =  $this->db->query("DELETE FROM booked_loan_data where bold_data_id in ($in_id_unbook) and bold_data_id not in ($in_id) and bold_type=1");
		}else{
			for($i=0;$i<$count;$i++){
				if($booked_month[$i]=='NULL' or $booked_month[$i]==''){
					echo "<script type='text/javascript'>window.alert('Harap isi Booked Month terlebih dahulu.');window.location.href = '".$_SERVER['HTTP_REFERER']."';</script>";
				}else{
					$insert =  $this->db->query("INSERT INTO `booked_loan_data` (`bold_data_id`, `bold_type`, `bold_booked_month`) select lfd_id,2,$booked_month[$i] from line_facility_data where lfd_id = $book[$i] and lfd_id not in (select bold_data_id from booked_loan_data where bold_type=2)");
					$update_booked_month = $this->db->query("UPDATE booked_loan_data set bold_booked_month = '$booked_month[$i]' where bold_data_id = $book[$i] and bold_type=2");
				}
			}
			$delete =  $this->db->query("DELETE FROM booked_loan_data where bold_data_id in ($in_id_unbook) and bold_data_id not in ($in_id) and bold_type=2");
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	public function user_data(){
 		if($this->session->userdata('status')!="login"){
 			redirect(base_url().'login');
 		}
		$data['user_data'] = $this->db->query("select * from user_data")->result();
		$this->load->view('include/header_bo');
		$this->load->view('include/navbar_bo');
		$this->load->view('user_data',$data);
		$this->load->view('include/footer_bo');
	}
	public function loans(){
 		if($this->session->userdata('status')!="login"){
 			redirect(base_url().'login');
 		}
		$data['inv_loan'] = $this->db->query("select loan_data.*,if(bold_id is not null,'Y','N') booked from loan_data left join booked_loan_data on bold_data_id = ld_id and bold_type = 1 where ld_product_id not in (4,5) order by ld_id desc")->result();
		$data['wctl_loan'] = $this->db->query("select loan_data.*,if(bold_id is not null,'Y','N') booked from loan_data left join booked_loan_data on bold_data_id = ld_id and bold_type = 1 where ld_product_id =4 order by ld_id desc")->result();
		$data['osf_loan'] = $this->db->query("select loan_data.*,if(bold_id is not null,'Y','N') booked from loan_data left join booked_loan_data on bold_data_id = ld_id and bold_type = 1 where ld_product_id =5 order by ld_id desc")->result();
		$this->load->view('include/header_bo');
		$this->load->view('include/navbar_bo');
		$this->load->view('loans',$data);
		$this->load->view('include/script_ext');
		$this->load->view('include/footer_bo');
	}
	public function line_based(){
		$url = $this->uri->segment(3); 
		$id = $this->uri->segment(4); 

 		if($this->session->userdata('status')!="login"){
 			redirect(base_url().'login');
 		}
		$data['product'] = $this->db->query("select * from mr_product")->result();
		$data['product2'] = $this->db->query("select * from mr_product")->result();
		$data['contract'] = $this->db->query("select * from mr_contract")->result();
		$data['contract2'] = $this->db->query("select * from mr_contract")->result();
		$data['lf_data'] = $this->db->query("SELECT lfd_id,lfd_number,lfd_product_id,lfd_contract_id,concat(mp_product_name,' - ',mc_contract_name) product,lfd_borrower_name,lfd_cap_amount,bold_booked_month,lfd_rm,lfd_vp,if(bold_id is not null,'Y','N') booked FROM line_facility_data left join booked_loan_data on bold_data_id = lfd_id and bold_type = 2 left join mr_product on mp_id = lfd_product_id left join mr_contract on mc_id = lfd_contract_id")->result();
		$this->load->view('include/header_bo');
		$this->load->view('include/navbar_bo');
		$this->load->view('line_based',$data);
		$this->load->view('include/footer_bo');

		if(isset($_POST['submit'])){
			$nama_barang = $this->input->post('nama_barang');
			$kategori = $this->input->post('kategori');
			$harga = $this->input->post('harga_barang');
			$desc = $this->input->post('desc');
			$stok = $this->input->post('stok');
			$berat = $this->input->post('berat');
			$path = $_FILES['gambar_barang']['name'];
			$target_dir = getcwd()."/assets/images/accesoris/";
			if($url=='tambah'){
				$basename = date("YmdHms").'.'.pathinfo($path, PATHINFO_EXTENSION);
				$target_file = $target_dir.$basename;

				$upload = move_uploaded_file($_FILES["gambar_barang"]["tmp_name"], $target_file);

				$run = $this->db->query("INSERT INTO `barang_data`(`bd_id`, `bd_kategori`, `bd_nama_barang`, `bd_harga`, `bd_deskripsi`, `bd_stok`, `bd_berat`, `bd_gambar`) VALUES (NULL, $kategori, '$nama_barang', $harga, '$desc', $stok, $berat, '$basename');");
				echo "<script type='text/javascript'>window.alert('Tambah Barang Berhasil.');window.location.href = '".$_SERVER['HTTP_REFERER']."';</script>";

			}else if($url=='edit'){

				$id_barang = $_POST['id_barang'];

				if($_FILES['gambar_barang']['size'] == 0){
					$run = $this->db->query("UPDATE `barang_data` SET `bd_kategori` = $kategori, `bd_nama_barang` = '$nama_barang', `bd_harga` = $harga, `bd_deskripsi` = '$desc', `bd_stok` = $stok, `bd_berat` = $berat WHERE `bd_id` = $id_barang;");
				}else{
					$basename = date("YmdHms").'.'.pathinfo($path, PATHINFO_EXTENSION);
					$target_file = $target_dir.$basename;

					$upload = move_uploaded_file($_FILES["gambar_barang"]["tmp_name"], $target_file);


					$run = $this->db->query("UPDATE `barang_data` SET `bd_kategori` = $kategori, `bd_nama_barang` = '$nama_barang', `bd_harga` = $harga, `bd_deskripsi` = '$desc', `bd_stok` = $stok, `bd_berat` = $berat, `bd_gambar` = '$basename' WHERE `bd_id` = $id_barang;");
				}
				echo "<script type='text/javascript'>window.alert('Update Barang Berhasil.');window.location.href = '".$_SERVER['HTTP_REFERER']."';</script>";
			}
		}
		if($url=='hapus'){
			echo "<script type='text/javascript'>
			if (confirm('Anda yakin hapus barang?')) {
			  // Save it!
			".$this->db->query("DELETE from barang_data WHERE `bd_id` = $id")."
			  window.location.href = '".base_url()."data_barang';
			} else {
			  // Do nothing!
			  window.location.href = '".base_url()."data_barang';
			}
			</script>";
		}
	}
	public function claimed(){
		$url = $this->uri->segment(3); 
		$id = $this->uri->segment(4); 

 		if($this->session->userdata('status')!="login"){
 			redirect(base_url().'login');
 		}
		$data['claimed'] = $this->db->query("SELECT
												ifnull( ld_loan_number, lfd_number ) loan_number,
												concat( mp_product_name, ' - ', mc_contract_name ) product,
												ifnull(ld_borrower_name,lfd_borrower_name) borrower_name,
												ifnull(rm.ud_fullname,lfd_rm) rm,
												ifnull(vp.ud_fullname,lfd_vp) vp,
												ifnull(ld_mpf_rate,lfd_mpf_rate) mpf_rate,
												ifnull(ld_mpf_rate/100*ld_loan_amount,lfd_mpf_rate/100*lfd_cap_amount) mpfee,
												ifnull(ld_loan_amount,lfd_cap_amount) amount,
												ld_disbursed_date disbursed_date,
												bold_booked_month booked_month
											FROM
												booked_loan_data
												LEFT JOIN loan_data ON ld_id = bold_data_id AND bold_type = 1
												LEFT JOIN line_facility_data ON lfd_id = bold_data_id AND bold_type = 2
												LEFT JOIN mr_product ON mp_id = ifnull( ld_product_id, lfd_product_id )
												LEFT JOIN mr_contract ON mc_id = ifnull(ld_contract_id,lfd_contract_id)
												left join user_data rm on rm.ud_id = ld_rm
												left join user_data vp on vp.ud_id = ld_vp")->result();
		$this->load->view('include/header_bo');
		$this->load->view('include/navbar_bo');
		$this->load->view('claimed',$data);
		$this->load->view('include/footer_bo');

		if(isset($_POST['submit'])){
			$kategori = $this->input->post('kategori');
			if($url=='tambah'){
				$run = $this->db->query("INSERT INTO `kategori_barang`(`kb_id`, `kb_nama_kategori`, `kb_created_date`) VALUES (NULL, '$kategori', NOW());
");
				echo "<script type='text/javascript'>window.alert('Tambah Kategori Berhasil.');window.location.href = '".$_SERVER['HTTP_REFERER']."';</script>";

			}else if($url=='edit'){
				$run = $this->db->query("UPDATE `kategori_barang` SET `kb_nama_kategori` = '$kategori', `kb_created_date` = NOW() WHERE `kb_id` = $id;
");
				echo "<script type='text/javascript'>window.alert('Update Kategori Berhasil.');window.location.href = '".$_SERVER['HTTP_REFERER']."';</script>";
			}
		}
		if($url=='hapus'){
			echo "<script type='text/javascript'>
			if (confirm('Anda yakin hapus kategori?')) {
			  // Save it!
			".$this->db->query("DELETE from kategori_barang WHERE `kb_id` = $id")."
			  window.location.href = '".base_url()."kategori';
			} else {
			  // Do nothing!
			  window.location.href = '".base_url()."kategori';
			}
			</script>";
		}
	}
	public function lf(){
		$url = $this->uri->segment(3); 
		$lf_id = $this->uri->segment(4);
		$lf_number= $this->input->post('lf_number');
		$product = $this->input->post('product');
		$borrower_name = $this->input->post('borrower_name');
		$contract = $this->input->post('contract');
		$rm = $this->input->post('rm');
		$vp = $this->input->post('vp');
		$cap = $this->input->post('cap');
		$user = $this->session->userdata('id');

		if($url=='add'){
			$run = $this->db->query("INSERT INTO line_facility_data(lfd_number,lfd_borrower_name, `lfd_product_id`, `lfd_contract_id`, lfd_rm, lfd_vp, lfd_cap_amount) VALUES ('$lf_number','$borrower_name','$product','$contract','$rm','$vp','$cap');
	");
			if($run){
				echo "<script type='text/javascript'>window.alert('Add Line Facility Successfully.');window.location.href = '".$_SERVER['HTTP_REFERER']."';</script>";
			}else{
				echo "<script type='text/javascript'>window.alert('Add Line Facility Failed.');window.location.href = '".$_SERVER['HTTP_REFERER']."';</script>";
			}
		}else if($url=='edit'){
			$run = $this->db->query("UPDATE line_facility_data set lfd_number = '$lf_number', lfd_borrower_name= '$borrower_name', `lfd_product_id` ='$product', `lfd_contract_id` = '$contract', lfd_rm = '$rm', lfd_vp = '$vp', lfd_cap_amount= '$cap', lfd_updated_by =$user where lfd_id = $lf_id");
			if($run){
				echo "<script type='text/javascript'>window.alert('Update Line Facility Successfully.');window.location.href = '".$_SERVER['HTTP_REFERER']."';</script>";
			}else{
				echo "<script type='text/javascript'>window.alert('Update Line Facility Failed.');window.location.href = '".$_SERVER['HTTP_REFERER']."';</script>";
			}
		}else{
			$run = $this->db->query("DELETE from line_facility_data where lfd_id = $lf_id");
			if($run){
				echo "<script type='text/javascript'>window.alert('Delete Line Facility Successfully.');window.location.href = '".$_SERVER['HTTP_REFERER']."';</script>";
			}else{
				echo "<script type='text/javascript'>window.alert('Delete Line Facility Failed.');window.location.href = '".$_SERVER['HTTP_REFERER']."';</script>";
			}
		}

	}
	public function login_act()
	{
		$nik = $this->input->post('nik');
		$password = $this->input->post('password');

		$cek = $this->db->query("SELECT * FROM user_data WHERE ud_no_induk='$nik' and ud_password = sha1('$password')")->row();

		if($cek){
		$id = $cek->ud_id;
		$gdata = $this->db->query("SELECT * from user_data where ud_id = $id")->row();
			$session = array(
				'id' 		=> $cek->ud_id,
				'nik'		=> $cek->ud_nik,
				'nama'		=> $cek->ud_fullname,
				'level'		=> $cek->ud_level,
				'status'	=> 'login'
			);
			$this->session->set_userdata($session);
			redirect(base_url());
		}else{
			redirect(base_url()."login?alert=wrong");
		}
	}

	function logout(){
		$this->session->sess_destroy();
		redirect(base_url().'backoffice');
	}
}
