<?php require_once("server.php"); ?>

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
if(isset($_GET['pseudo'])) {
	$pseudo = $_GET['pseudo'];
	$req = $bdd->prepare('SELECT * FROM MEMBERS WHERE login = ?');
	$req->execute(array($pseudo));
	$data = $req->fetch();
	if (!empty($data['id'])) {
		$bandate = date('Y-m-d H:i:s');
		$req = $bdd->prepare('UPDATE MEMBERS SET ban = ? WHERE id = ?');
		$req->execute(array($bandate, $data['id']));
	}
}
?>

<!-- Supprimer une modification (supprime aussi toutes les modifications postérieures sur ce produit) -->
<?php
if(isset($_GET['id_p']) && isset($_GET['version'])){
	$id_p = intval($_GET['id_p']);
	$version = intval($_GET['version']);
	$req = $bdd->prepare('SELECT * FROM PRODUCTS WHERE id_p = ? AND version = ?');
	$req->execute(array($id_p, $version));
	$data = $req->fetch();
	if (!empty($data['id_p'])){
		$req = $bdd->prepare('UPDATE PRODUCTS SET deletionDate = ? WHERE id_p = ? AND version >= ?');
		$req->execute(array(date("Y-m-d H:i:s"), $id_p, $version));
	}
}
?>

<!-- Effectuer un rollback de l'application -->
<?php
if(isset($_GET['date']) || (isset($_POST['rollback']) && $_POST['rollback'] == "Rollbacker" && isset($_POST['date']) && !empty($_POST['date'])) ){
	if (isset($_GET['date'])){
		$date = intval($_GET['date']);
		$date = date('Y-m-d H:i:s', $date);
	}
	else{
		$date = date('Y-m-d H:i:s', strtotime($_POST['date']));
	}
	$req = $bdd->prepare('SELECT * FROM PRODUCTS WHERE creationDate >= ?');
	$req->execute(array($date));
	while($d = $req->fetch()){
		echo $d['wording'];
	}
	$req = $bdd->prepare('DELETE FROM PRODUCTS WHERE creationDate >= ?');
	$req->execute(array($date));
	$req = $bdd->prepare('DELETE FROM COMPONENTS WHERE NOT EXISTS(SELECT * FROM PRODUCTS P WHERE P.id_p = COMPONENTS.id_p AND P.version = COMPONENTS.version)');
	$req->execute();
	$req = $bdd->prepare('UPDATE PRODUCTS SET deletionDate = NULL WHERE deletionDate >= ?');
	$req->execute(array($date));
	$req = $bdd->prepare('UPDATE COMPONENTS C SET C.deletionVersion = NULL WHERE C.deletionVersion IS NOT NULL AND NOT EXISTS(SELECT * FROM PRODUCTS P WHERE  P.id_p = C.id_p AND P.version = C.deletionVersion)');
	$req->execute();
}
?>

<?php
header ('Location: home.php');
exit();
?>
