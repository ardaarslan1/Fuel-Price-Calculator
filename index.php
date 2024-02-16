<?php
include_once "classes.php";
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
$class= new api();
$database = new Db();
$conn = $database->getConn();

$iller = $conn->query("SELECT * FROM iller")->fetchAll(PDO::FETCH_ASSOC);
$ilceler = $conn->query("SELECT * FROM ilceler")->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="post" action="">
    <select id="il" name="il">
        <option value="">İl Seçin</option>
        <?php foreach ($iller as $il): ?>
            <option value="<?php echo $il['id']; ?>"><?php echo $il['il_adi']; ?></option>
        <?php endforeach; ?>
    </select>

    <select id="ilce" name="ilce">
        <option value="">İlçe Seçin</option>
    </select>
    <select id="fuelType" name="fuelType">
        <option value="">Yakıt Seçin</option>
        <option value="diesel">Dizel</option>
        <option value="gasoline">Benzin</option>
    </select>
    <input type="submit" name="submit">
</form>

<?php
if(isset($_POST["submit"])){
    $response=$class->apiRequest($_POST["il"],$_POST["ilce"],$_POST["fuelType"]);
    foreach ($response as $item) {
        echo $item["productName"]."Litre Fiyatı: ".$item["amount"]."<br><br>";
    }
}
?>

<script>
    document.getElementById("il").addEventListener("change", function() {
        var ilID = this.value;

        var ilceSelect = document.getElementById("ilce");
        ilceSelect.innerHTML = "<option value=''>İlçe Seçin</option>";

        if (ilID !== "") {
            <?php foreach ($ilceler as $ilce): ?>
            if (<?php echo $ilce['il_id']; ?> == ilID) {
                var option = document.createElement("option");
                option.text = "<?php echo $ilce['ilce_adi']; ?>";
                option.value = "<?php echo $ilce['id']; ?>";
                ilceSelect.appendChild(option);
            }
            <?php endforeach; ?>
        }
    });
</script>