<?php require_once("server.php"); ?>

<?php
session_start();
if (!isset($_SESSION['login'])) {
	header ('Location: home.php');
	exit();
}
if (!isset ($_GET['id'])){
	header ('Location: home.php');
	exit();
}
else{
	$id_p = intval($_GET['id']);
	if (isset($_POST['modify']) && $_POST['modify'] == "Valider"){ // Si on vient de modifier un produit
		// On crée le nouveau produit
		$req = $bdd->prepare('SELECT MAX(version) FROM PRODUCTS WHERE id_p = ?');
		$req->execute(array($id_p));
		$data = $req->fetch();
		$req = $bdd->prepare('INSERT INTO PRODUCTS(id_p, version, wording, pseudo, creationDate) VALUES(:id_p, :version, :wording, :pseudo, :creationDate)');
			$req->execute(array(
			'id_p' => $id_p,
			'version' => $data[0]+1,
			'wording' => ucfirst(strtolower($_POST['wording'])),
			'pseudo' => $_SESSION['login'],
			'creationDate' => date('Y-m-d H:i:s')));
		// on gère les anciens composants

		if (isset($_POST['oldComponent'])){
			$oC = count($_POST['oldComponent']);
			$i = 0;
			while ($i < $oC){
				// si il est coché ou qu'un champs est vide, on met une version de suppression ($data[0]+1)
				if (isset($_POST['deleteIt'.$i])){
					$req = $bdd->prepare('SELECT * FROM COMPONENTS  WHERE id_p = ? AND deletionVersion IS NULL AND name = ?');
					$req->execute(array($id_p, $_POST['oldComponent'][$i]));
					$d1 = $req->fetch();
					$req = $bdd->prepare('UPDATE COMPONENTS SET deletionVersion = ? WHERE id_c = ?');
					$req->execute(array($data[0]+1, $d1['id_c']));
				}
				$i++;
			}
		}
		//on gère les nouveaux composants
		if (isset($_POST['newComponent'])){
			$nC = count($_POST['newComponent']);
			$i = 0;
			while ($i < $nC){
				if (!empty($_POST['newComponent'][$i]) && !empty($_POST['newQuantity'][$i])){
					// On vérifie que ce produit ne possède pas (ou plus) de composant qui porte ce name
					$req = $bdd->prepare('SELECT COUNT(*) FROM COMPONENTS C WHERE C.id_p = ? AND C.deletionVersion IS NULL AND C.name = ? AND EXISTS(SELECT * FROM PRODUCTS P WHERE P.id_p = C.id_p AND deletionDate IS NOT NULL)');
					$req->execute(array($id_p, $_POST['newComponent'][$i]));
					$d2 = $req->fetch();
					if ($d2[0] == 0){
						$req = $bdd->prepare('INSERT INTO COMPONENTS(id_p, version, name, quantity) VALUES(:id_p, :version, :name, :quantity)');
						$req->execute(array(
						'id_p' => $id_p,
						'version' => $data[0]+1,
						'name' => ucfirst(strtolower($_POST['newComponent'][$i])),
						'quantity' => $_POST['newQuantity'][$i]));
					}
				}
				$i++;
			}
		}
		header ('Location: product.php?id='.$id_p);
		exit();
	}
	else{
		$req = $bdd->prepare('SELECT COUNT(*) FROM PRODUCTS P1 WHERE P1.id_p = ? AND P1.version = (SELECT MAX(P2.version) FROM PRODUCTS P2 WHERE P2.id_p = P1.id_p AND P2.deletionDate IS NULL)');
		$req->execute(array($id_p));
		$data = $req->fetch();
		if ($data[0] != 1){
			header ('Location: home.php');
			exit();
		}
	}


}
?>


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
				<?php
				// On récupère le produit en question
				$req = $bdd->prepare('SELECT * FROM PRODUCTS P1 WHERE P1.id_p = ? AND P1.version = (SELECT MAX(P2.version) FROM PRODUCTS P2 WHERE P2.id_p = P1.id_p AND P2.deletionDate IS NULL)');
				$req->execute(array($id_p));
				$data = $req->fetch();
				// On récupère tous ses composants associés
				$req = $bdd->prepare('SELECT * FROM COMPONENTS C WHERE C.id_p = ? AND C.deletionVersion IS NULL AND EXISTS(SELECT * FROM PRODUCTS P WHERE P.id_p = C.id_p AND P.version = C.version AND deletionDate IS NULL)');
				$req->execute(array($id_p));
				?>
				<h2>Modifier le produit</h2>
				<form action="" method="post" id="modifyProductForm">
					<label>Libellé du produit</label>
					<input type="text" name="wording" value="<?php echo $data['wording']; ?>" required/>
					<label>Composants</label>
					<table>
						<thead>
							<tr>
								<th>Anciens composants</th>
								<th>Qté (g)</th>
								<th><img src="../img/trash-can.png"></th>
							</tr>
						</thead>
						<tbody id="addTable">
							<?php
							$i = 0;
							while($c = $req->fetch()){
								echo '
								<tr>
									<td class="tdModifyLeft"><input type="text" value="'.htmlspecialchars($c['name']).'" name="oldComponent[]" required readonly /></td>
									<td class="tdModifyMiddle"><input type="text" value="'.htmlspecialchars($c['quantity']).'" name="oldQuantity[]" required readonly /></td>
									<td class="tdModifyRight"><input type="checkbox" name="deleteIt'.$i.'" /></td>
								</tr>';
								$i++;
							}
							?>
							<tr>
								<th>Nouveaux composants</td>
								<th>Qté (g)</td>
							</tr>
							<tr id="1">
								<td><input type="text" name="newComponent[]" placeholder="vide"/></td>
								<td><input type="text" name="newQuantity[]" /></td>
							</tr>

						</tbody>
					</table>
					<button type="button" id="addComponentButton" class="addButton">Ajouter un composant</button>
					<button type="button" id="removeComponentButton" class="addButton">Retirer un composant</button>
					<input type="submit" name="modify" value="Valider"/>
				</form>
			</div>
		</div>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/modify.js"></script>
		<script type="text/javascript" src="../js/home.js"></script>
	</body>
</html>
