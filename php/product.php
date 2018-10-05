<?php require_once("server.php"); ?>

<?php
session_start();
if (!isset($_GET['id'])){
	$id_p = -1;
}
else{
	$id_p = intval($_GET['id']);
	$req = $bdd->prepare('SELECT COUNT(*) FROM PRODUCTS WHERE id_p = ? AND deletionDate IS NULL');
	$req->execute(array($id_p));
	$data = $req->fetch();
	if ($data[0] == 0){
		$id_p = -1;
	}
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Foodissimo</title>
		<link rel="icon" type="image/png" href="../img/shiny-apple.png" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/design.css">
		<link rel="stylesheet" type="text/css" href="../css/nav.css">
		<link rel="stylesheet" type="text/css" href="../css/form.css">
		<link rel="stylesheet" type="text/css" href="../css/table.css">
		<script type="text/javascript" src="../js/jquery.js"></script>

	</head>
	<body>
		<nav>
			<ul>
				<li><button id="hamburger" /></li>
				<li><a href="home.php"><img src="../img/foodissimo.png"></a></li>
				<li><a href="home.php">Galerie</a></li>
				<?php if (isset($_SESSION['login'])){ ?>
				<li><a href="add.php">Ajouter un produit</a></li>
				<li><a href="disconnection.php"><img src="../img/cancel.png"></a></li>
				<?php } ?>
			</ul>
		</nav>
		<div id="all">
			<div id="left">
				<?php
				function escape($valeur){
					return htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8', false);
				}
				if ($id_p == -1){
					echo '<h1>Produit Inexistant</h1>';
				}
				else{
					$req = $bdd->prepare('SELECT * FROM PRODUCTS WHERE id_p = ? AND version = (SELECT MAX(version) FROM PRODUCTS WHERE id_p = ? AND deletionDate IS NULL)');
					$req->execute(array($id_p, $id_p));
					$data = $req->fetch();
					if (isset($_SESSION['id'])){
						echo '
						<button id="modifyButton" data-id_p="'.$id_p.'"><img src="../img/cog.png"></button>';
					}
					echo '
					<h1>'.escape($data['wording']).'</h1>
					<h3>Composants actuels :</h3>
					<div id="currentComponents">
						<table>
							<tr>
								<th>Composants</th>
								<th>Qté (g)</th>
							</tr>
							';
							$req = $bdd->prepare('SELECT name, quantity FROM COMPONENTS C WHERE C.id_p = ? AND C.deletionVersion IS NULL AND EXISTS(SELECT * FROM PRODUCTS P WHERE P.id_p = C.id_p AND P.version = C.version AND deletionDate IS NULL) ORDER BY quantity DESC');
							$req->execute(array($id_p));
							while($d = $req->fetch()){
								echo '
								<tr>
									<td class="tdProductLeft">'.escape($d['name']).'</td>
									<td class="tdProductRight">'.escape($d['quantity']).'</td>
								</tr>';
							}
							echo '
						</table>
					</div>';
				}
				?>
			</div>
			<div id="right">
				<?php
				if (!isset($_SESSION['login'])) {
					echo '<h3>Connectez-vous pour pouvoir visualiser l\'historique de ce produit.</h3>';
				}
				else if ($id_p != -1){
					echo '
					<button id="exportButton"><img src="../img/cloud-download.png"></button>
					<h2>Historique</h2>';
					$req = $bdd->prepare('SELECT * FROM PRODUCTS WHERE id_p = ? AND version <= ? AND deletionDate IS NULL ORDER BY version DESC');
					$req->execute(array($id_p, $data['version']));
					while($d = $req->fetch()){
						echo '
						<div id="historicalProduct">
							<div id="historicalProductDate">
								'.$d['creationDate'].'
							</div>';
							if ($_SESSION['admin'] == true){
								echo '
							<button id="deleteButton" data-id_p="'.$d['id_p'].'" data-version="'.$d['version'].'"><img src="../img/trash-can.png"></button>';
							}
							echo '
							<div id="historicalProductLogin">
								par '.$d['pseudo'].'
							</div>';
							if ($_SESSION['admin'] == true){
								echo '
							<button id="banButton" data-pseudo="'.$d['pseudo'].'"><img src="../img/siren.png"></button>
							<button id="rollbackButton" data-date="'.strtotime($d['creationDate']).'"><img src="../img/backward-time.png"></button>';
							}
							echo '
						</div><br><br><br>
						<div id="historicalComponents">';
						$reqc = $bdd->prepare('SELECT name, quantity FROM COMPONENTS C WHERE C.id_p = ? AND version <= ? AND (C.deletionVersion IS NULL OR C.deletionVersion > ?) AND EXISTS(SELECT * FROM PRODUCTS P WHERE P.id_p = C.id_p AND P.version = C.version AND deletionDate IS NULL) ORDER BY quantity DESC');
						$reqc->execute(array($id_p, $d['version'], $d['version']));
						while($c = $reqc->fetch()){
							echo '
							<u>'.escape($c['name']).'</u> ('.escape($c['quantity']).'g) ';
						}
						echo '
						</div><br><br>';
					}
				}
				?>
			</div>
		</div>
	<script type="text/javascript" src="../js/product.js"></script>
	<script type="text/javascript" src="../js/home.js"></script>
	</body>
</html>
