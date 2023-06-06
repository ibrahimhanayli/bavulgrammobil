<?php
// CORS ayarları
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Veritabanı bağlantısı
$servername = "localhost";
$dbname = "lezzetse_lezzetsepeti";
$username = "lezzetse_r1";
$password = "123.iBo.123";

/*
    Kategoriler
    1-Yemekler
    2-İçecekler
    3-Soslar
    4-Atıştırmalıklar
    5-Tatlılar

    Durumlar
    0-Sipariş Alındı
    1-Sipariş Hazırlanıyor
    2-Sipariş Yolda
    3-Sipariş Teslim Edildi
    4-Sipariş İptal Edildi
*/

$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // CORS ayarları
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST");
    header("Access-Control-Allow-Headers: Content-Type");
    exit;
}
// Gelen isteği işle
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // GET metodu ile ilgili işlemler burada gerçekleştirilir

    switch ($_GET['action']) {
        case 'yemeklerigetir':
            {
                // Yemekleri getir
                $sql = "SELECT * FROM yemekler order by kategori";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result);
            }
            break;
        case 'takip':
            {
                $takip = $_GET['takip'];
                $sql = "SELECT * FROM siparisler WHERE takip = '$takip'";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result);
            }
            break;
        case 'adminsiparisler':
            {
                // Siparişleri getir
                $sql = "SELECT s.id as id, m.ad as ad, m.soyad as soyad, s.toplamucret as tutar, s.durum as durum, s.takip as takip, s.siparistarihi as tarih, s.sepet as sepet FROM siparisler s, musteriler m WHERE s.musteri = m.id order by siparistarihi desc";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result);
            }
            break;
        case 'adminsiparisdetay':
            {
                $sepet = explode(",", base64_decode($_GET['sepet']));
                $sql = "SELECT * FROM yemekler WHERE id IN (".implode(',',$sepet).")";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result);
            }
            break;
        case 'adminpaneldetay':
            {
                $sql = "SELECT 
                (SELECT SUM(toplamucret) FROM siparisler WHERE durum=3 and DATE(siparistarihi) = CURDATE()) as gkazanc,
                (SELECT COUNT(id) FROM siparisler WHERE durum=3 and DATE(siparistarihi) = CURDATE()) as gsiparis,
                (SELECT SUM(toplamucret) FROM siparisler where durum=3) as tkazanc, 
                (SELECT COUNT(id) FROM siparisler where durum=3) as tsiparis FROM siparisler limit 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result);
            }
            break;
        case 'yemekgetir':
            {
                $yemekid = $_GET['id'];
                $sql = "SELECT * FROM yemekler WHERE id = $yemekid";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result[0]);
            }
            break;
        default:
            // Desteklenmeyen bir işlem geldiğinde hata döndürme
            http_response_code(400);
            echo 'Desteklenmeyen bir işlem!';
            exit;


    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    switch ($_GET['action']) {
        case 'siparisolustur':
            {
                $ad = $_POST['ad'];
                $soyad = $_POST['soyad'];
                $adres = $_POST['adres'];
                $telefon = $_POST['telefon'];
                $sepet = $_POST['sepet'];
                $toplamucret = $_POST['toplamucret'];

                //musteri ekle
                $sql = "INSERT INTO musteriler (ad, soyad, adres, telefon) VALUES ('$ad', '$soyad', '$adres', $telefon)";
                $stmt = $conn->prepare($sql);
                if($stmt->execute()){
                    $musteriid = $conn->lastInsertId();
                    //siparis ekle
                    $takip = strtoupper(uniqid());
                    $sql = "INSERT INTO siparisler (sepet,toplamucret,durum,musteri,takip) VALUES ('$sepet', ".$toplamucret.", 0, ".$musteriid.", '$takip')";
                    $stmt = $conn->prepare($sql);
                    if($stmt->execute()){
                        echo json_encode(array('durum' => 1,'mesaj' => 'Siparişiniz alındı.', 'takip' => $takip));
                        exit;
                    }else{
                        echo json_encode(array('durum' => 0,'mesaj' => 'Siparişiniz alınamadı.'));
                        exit;
                    }

                }else{
                    echo json_encode(array('durum' => 0,'mesaj' => 'Müşteri eklenemedi.'));
                    exit;
                }

            }
            break;
        case 'siparisdurumguncelle':
            {
                $siparisid = $_POST['id'];
                $durum = $_POST['durum'];

                //siparis durumunu güncelle
                $sql = "UPDATE siparisler SET durum = $durum WHERE id = $siparisid";
                if($durum == 3){
                    $sql = "UPDATE siparisler SET durum = $durum, teslimtarihi = NOW() WHERE id = $siparisid";
                }
                $stmt = $conn->prepare($sql);
                if($stmt->execute()){
                    echo json_encode(array('durum' => 1,'mesaj' => 'Sipariş durumu güncellendi.'));
                    exit;
                }else{
                    echo json_encode(array('durum' => 0,'mesaj' => 'Sipariş durumu güncellenemedi.'));
                    exit;
                }
            }
            break;
        case 'yemeksil':
            {
                $yemekid = $_POST['id'];

                //siparis durumunu güncelle
                $sql = "DELETE FROM yemekler WHERE id = $yemekid";
                $stmt = $conn->prepare($sql);
                if($stmt->execute()){
                    echo json_encode(array('durum' => 1,'mesaj' => 'Sipariş silindi.'));
                    exit;
                }else{
                    echo json_encode(array('durum' => 0,'mesaj' => 'Sipariş silinemedi.'));
                    exit;
                }
            }
            break;
        case 'yemekekle':
            {
                $ad = $_POST['ad'];
                $aciklama = $_POST['aciklama'];
                $kategori = $_POST['kategori'];
                $fiyat = $_POST['fiyat'];

                //yemek ekle
                $sql = "INSERT INTO yemekler (ad, aciklama, kategori, fiyat) VALUES ('$ad', '$aciklama', $kategori, $fiyat)";
                $stmt = $conn->prepare($sql);
                if($stmt->execute()){
                    echo json_encode(array('durum' => 1,'mesaj' => 'Yemek eklendi.'));
                    exit;
                }else{
                    echo json_encode(array('durum' => 0,'mesaj' => 'Yemek eklenemedi.'));
                    exit;
                }
            }
            break;
        case 'yemekguncelle':
            {
                $ad = $_POST['ad'];
                $aciklama = $_POST['aciklama'];
                $kategori = $_POST['kategori'];
                $fiyat = $_POST['fiyat'];
                $id = $_POST['id'];

                //yemek ekle
                $sql = "UPDATE yemekler SET ad = '$ad', aciklama = '$aciklama', kategori = $kategori, fiyat = $fiyat WHERE id = $id";
                $stmt = $conn->prepare($sql);
                if($stmt->execute()){
                    echo json_encode(array('durum' => 1,'mesaj' => 'Yemek güncellendi.'));
                    exit;
                }else{
                    echo json_encode(array('durum' => 0,'mesaj' => 'Yemek güncellenemedi.'));
                    exit;
                }
            }
            break;
        default:
            {
                // Desteklenmeyen bir işlem geldiğinde hata döndürme
            http_response_code(400);
            echo 'Desteklenmeyen bir işlem!';
            exit;
            }
            break;
    }
} else {
    // Desteklenmeyen bir HTTP metodu geldiğinde hata döndürme
    http_response_code(405);
    echo 'Desteklenmeyen bir metot!';
}
