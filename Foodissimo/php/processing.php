<?php
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=projet;charset=utf8', 'root', '');
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}
?>

<?php
session_start();  
if (!isset($_SESSION['login'])) { 
   header ('Location: home.php'); 
   exit();  
}
else if ($_SESSION['admin'] == false){
	header ('Location: home.php'); 
	exit(); 
}
?>

<!-- Bannir un utilisateur -->
<?php
if(isset($_GET['id'])) {
	$id = intval($_GET['id']);
	$req = $bdd->prepare('SELECT * FROM MEMBERS WHERE id = ?');
	$req->execute(array($id));
	$data = $req->fetch();
	if (!empty($data['id'])) {
		$req = $bdd->prepare('UPDATE MEMBERS SET ban=CURDATE() WHERE id = ?');
		$req->execute(array($data['id']));
	}
}
?>

<!-- Supprimer une modification -->
<?php
if(isset($_GET[id_p]) && isset($_GET[version])){
	
}