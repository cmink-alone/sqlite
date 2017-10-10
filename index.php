<?php
require_once("koneksi.php");

$db = new SQLiteDatabase('test.sqlite');
//membuka file database sqlite sekaligus membuat file baru jika file tidak ditemukan
$count = $db->get_table_count();
//mendapatkan jumlah tabel pada database
if($count < 1){
	//jika jumlah tabel kurang dari 1
	//buat tabel baru dengan nama users dan membuat array baru untuk tiap kolom database
	$db->create_table("users",array('id' => 'INTEGER(10)',
	'nama' => 'VARCHAR(50)'
	));
	
}

//Menambahan Data ke tabel users
$db->add_data('users',array('id'=>'1','nama'=>'Muhammad Sayuti'));

//Menampilkan Data dalam array
print_r($db->get_data('users'));

//Mengubah nama user dengan id 1
if($db->update_data('users',array('nama'=>'Sukirman'),array('id'=>'1'))){
	
	echo "<br />";
	
	print_r($db->get_data('users'));
	
}

//menghapus semua data pada tabel users
if($db->delete_data('users')){
	
	print_r('<br />data berhasil dihapus');
	
}

?>