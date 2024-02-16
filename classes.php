<?php
$method="post";
$hostName="localhost";
$hostUsername="root";
$hostPass="root";

class Db
{
    protected $conn;

        public function __construct()
        {
            global $hostName;
            global $hostUsername;
            global $hostPass;
            try {
                $this->conn = new PDO("mysql:host=$hostName;dbname=testet" , "$hostUsername" , "$hostPass");
            } catch (PDOException $e) {
                die("DB ERROR1: " . $e->getMessage());
            }

        }
    public function getConn() {
        return $this->conn;
    }

}
class api extends Db
{
    public function tr_strtoupper($string) {
        $search = array('ç', 'ğ', 'ı', 'i', 'ö', 'ş', 'ü');
        $replace = array('Ç', 'Ğ', 'I', 'İ', 'Ö', 'Ş', 'Ü');
        return strtoupper(str_replace($search, $replace, $string));
    }

    public function apiRequest($city , $district , $fuel)
    {
        $url = 'https://api.opet.com.tr/api/fuelprices/allprices';
        $json_veri = file_get_contents($url);
        $dizi_veri = json_decode($json_veri , true);

        $database = new Db();
        $conn = $database->getConn();

        $iller = $conn->query("SELECT * FROM iller WHERE id=$city")->fetchAll(PDO::FETCH_ASSOC);
        $ilceler = $conn->query("SELECT * FROM ilceler WHERE il_id=$city")->fetchAll(PDO::FETCH_ASSOC);

        $cityName = '';
        $districtName = '';

        foreach ($iller as $il) {
            $cityName  = $this->tr_strtoupper($il['il_adi']);

        }

        foreach ($ilceler as $ilce) {
            $districtName ="MERKEZ";
        }

        $response = [];

        foreach ($dizi_veri as $veri) {
            if ($veri['provinceName'] == $cityName && $veri['districtName'] == $districtName) {
                foreach ($veri['prices'] as $fiyat) {
                    if ($fuel == "gasoline" && $fiyat['productName'] == "Kurşunsuz Benzin 95") {
                        $response[] = $fiyat;
                    }
                    if ($fuel == "diesel" && ($fiyat['productName'] == "Motorin UltraForce" || $fiyat['productName'] == "Motorin EcoForce")) {
                        $response[] = $fiyat;
                    }
                }
            }
        }

        return $response;
    }

}
?>