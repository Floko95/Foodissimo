<?php require_once("server.php"); ?>

<?php
session_start();
if (!isset($_SESSION['login'])) {
   header ('Location: home.php');
   exit();
}
?>

<!-- AJOUT D'UN NOUVEAU PRODUIT -->
<?php
if (isset($_POST['add']) && $_POST['add'] == "Valider"){
	if (isset($_POST['wording']) && !empty($_POST['wording'])){
		$req = $bdd->prepare('SELECT COUNT(*) FROM PRODUCTS WHERE wording = ? AND deletionDate IS NULL');
		$req->execute(array($_POST['wording']));
		$data = $req->fetch();
		if ($data[0] == 0){
			// On crée le produit
			$req = $bdd->prepare('SELECT MAX(id_p) FROM PRODUCTS');
			$req->execute();
			$id_p = $req->fetch();
			$id_p = $id_p[0] + 1;
			$date = date('Y-m-d H:i:s');
			$req = $bdd->prepare('INSERT INTO PRODUCTS(id_p, version, wording, pseudo, creationDate) VALUES(:id_p, :version, :wording, :pseudo, :creationDate)');
			$req->execute(array(
			'id_p' => $id_p,
			'version' => 1,
			'wording' => ucfirst(strtolower($_POST['wording'])),
			'pseudo' => $_SESSION['login'],
			'creationDate' => $date));
			// On ajoute les composants
			if (isset($_POST['component'])){
				$n = count($_POST['component']);
				$i = 0;
				while ($i < $n){
					if (!empty($_POST['component'][$i]) && !empty($_POST['quantity'][$i])){
						// On fait attention à ce que l'utilisateur n'entre pas deux fois le même nom
						$req = $bdd->prepare('SELECT COUNT(*) FROM COMPONENTS WHERE id_p = ? AND version = 1 AND name = ?');
						$req->execute(array($id_p, ucfirst(strtolower($_POST['component'][$i]))));
						$d = $req->fetch();
						if ($d[0] == 0){
							$req = $bdd->prepare('INSERT INTO COMPONENTS(id_p, version, name, quantity) VALUES(:id_p, :version, :name, :quantity)');
							$req->execute(array(
							'id_p' => $id_p,
							'version' => 1,
							'name' => ucfirst(strtolower($_POST['component'][$i])),
							'quantity' => $_POST['quantity'][$i]));
						}
					}
					$i++;
				}
			}
			header ('Location: product.php?id='.$id_p);
			exit();
		}
		else{
			$errorAdd = 'Un produit possède déjà ce nom.';
		}
	}
	else{
		$errorAdd = 'Veuillez entrer le libellé du produit !';
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
			</div>
			<div id="right">
				<h2>Ajouter un produit</h2>
				<?php if (isset($errorAdd)) echo $errorAdd; ?>
				<form action="add.php" method="post" id="addProductForm">
					<fieldset>
						<legend>Libellé du produit</legend>
						<input type="text" name="wording" placeholder="Nom du produit" required/>
					</fieldset>
					<fieldset>
						<legend>Composants</legend>
						<table>
							<thead>
								<tr>
									<th>Nom du composant</th>
									<th>Quantité (g)</th>
								</tr>
							</thead>
							<tbody id="addTable">
								<tr id="1">
									<td class="tdLeft"><input type="text" name="component[]" placeholder="vide"/></td>
									<td><input type="text" name="quantity[]" /></td>
								</tr>
								<tr id="2">
									<td class="tdLeft"><input type="text" name="component[]" placeholder="vide"/></td>
									<td><input type="text" name="quantity[]" /></td>
								</tr>
								<tr id="3">
									<td class="tdLeft"><input type="text" name="component[]" placeholder="vide"/></td>
									<td><input type="text" name="quantity[]" /></td>
								</tr>
							</tbody>
						</table>
						<button type="button" id="addComponentButton" class="addButton">Ajouter un composant</button>
						<button type="button" id="removeComponentButton" class="addButton">Retirer un composant</button>
					</fieldset>
					<input type="submit" name="add" value="Valider"/>
				</form>
			</div>
		</div>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/add.js"></script>
		<script type="text/javascript" src="../js/home.js"></script>
	</body>
</html>
