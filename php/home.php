<?php require_once("server.php"); ?>

<?php session_start(); ?>

<!-- INSCRIPTION -->
<?php
if (isset($_POST['validation']) && $_POST['validation'] == 'Valider') {
	if ((isset($_POST['iLogin']) && !empty($_POST['iLogin'])) && (isset($_POST['iPassword']) && !empty($_POST['iPassword'])) && (isset($_POST['iConfirm']) && !empty($_POST['iConfirm']))) {
		if ($_POST['iPassword'] != $_POST['iConfirm']) {
			$errorInscription = 'Les deux mots de passe sont diff&eacute;rents.';
		}
		else {
			$req = $bdd->prepare('SELECT COUNT(*) FROM MEMBERS WHERE login = ?');
			$req->execute(array($_POST['iLogin']));
			$data = $req->fetch();
			if ($data[0] == 0) {
				$req = $bdd->prepare('INSERT INTO MEMBERS(login, password) VALUES(:login, :password)');
				$req->execute(array(
				'login' => $_POST['iLogin'],
				'password' => sha1($_POST['iPassword'])));
				session_start();
				$_SESSION['login'] = $_POST['iLogin'];
				$req = $bdd->prepare('SELECT * FROM MEMBERS WHERE login = ?');
				$req->execute(array($_SESSION['login']));
				$donnees = $req->fetch();
				$_SESSION['id'] = $donnees['id'];
				$_SESSION['admin'] = $donnees['admin'];
				header('Location: home.php');
				exit();
			}
			else {
				$errorInscription = 'Un membre poss&egrave;de déjà ce login.';
			}
		}
	}
	else {
		$errorInscription = 'Au moins un des champs est vide.';
	}
}
?>

<!-- CONNEXION -->
<?php
	if(isset($_POST['connection']) && $_POST['connection'] == 'Connection'){
		if(isset($_POST['cLogin'], $_POST['cPassword']) && !empty($_POST['cLogin']) && !empty($_POST['cPassword']))
		{
			$login = $_POST['cLogin'];
			$password = sha1($_POST['cPassword']);
			$req = $bdd->prepare('SELECT COUNT(*) FROM MEMBERS WHERE login = ?');
			$req->execute(array($login));
			$data = $req->fetch();
			if($data[0] == 1){
				$req = $bdd->prepare('SELECT * FROM MEMBERS WHERE login = ?');
				$req->execute(array($login));
				$data = $req->fetch();
				if($data['password'] == $password)
				{
					if($data['ban'] == null)
					{
						session_start();
						$_SESSION['login'] = $data['login'];
						$_SESSION['id'] = $data['id'];
						$_SESSION['admin'] = $data['admin'];
						header('Location: home.php');
						exit();
					}
					else
					{
						$errorConnection = 'Ce compte est banni de manière définitive !';
					}
				}
				else
				{
					$errorConnection = 'Pseudo ou mot de passe incorrect !';
				}
			}
			else
			{
				$errorConnection = 'Pseudo ou mot de passe incorrect !';
			}
		}
		else{
			$errorConnection = 'Au moins un des champs est vide.';
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
				<?php
				if (!isset($_SESSION['login'])){
					echo '
					<h1>Menu</h1>
					<ul>
						<li><button id="b_home">Galerie</button></li>
						<li><button id="b_connection">Se connecter</button></li>
						<li><button id="b_inscription">Créer un compte</button></li>
					</ul>
					';
				}
				?>
				<h1>Recherche</h1><br><br>
				<form action="home.php" method="post" id="searchProductForm">
						<label>Libellé du produit</label>
						<input type="text" id="wording" name="wording" placeholder="vide" class="searchInput" />
						<label>Composants</label>
						<input type="text" id="field1" name="field1" placeholder="vide" class="searchInput" />
						<input type="text" id="field2" name="field2" placeholder="vide" class="searchInput" />
						<input type="text" id="field3" name="field3" placeholder="vide" class="searchInput" />
						<input type="submit" id="searchSubmit" name="search" value="Rechercher" />
				</form>
				<?php
				if (isset($_SESSION['admin']) && $_SESSION['admin'] == true){
					echo '
					<h1>Rollbacker</h1>
					<form action="processing.php" method="post" id="rollbackForm">
						<input type="date" name="date" id="rollbackInput" required /><br>
						<input type="submit" id="rollbackSubmit" name="rollback" value="Rollbacker" />
					</form>';
				}
				?>
			</div>
			<div id="right">
				<div id="m_home">
					<?php
					function escape($valeur){
						return htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8', false);
					}
					if (isset($_POST['search']) && $_POST['search'] == 'Rechercher'){ // Si on a effectué une recherche
						if (isset($_POST['wording']) && isset($_POST['field1']) && isset($_POST['field2']) && isset($_POST['field3'])){
							$w = escape($_POST['wording']);
						}
						if (isset($_POST['field1']) && isset($_POST['field2']) && isset($_POST['field3']) && (!empty($_POST['field1']) || !empty($_POST['field2']) || !empty($_POST['field3']))){
							$f1 = escape($_POST['field1']);
							$f2 = escape($_POST['field2']);
							$f3 = escape($_POST['field3']);
							$req = $bdd->prepare('SELECT COUNT(*) FROM PRODUCTS P WHERE P.wording LIKE ? AND P.version = (SELECT MAX(version) FROM PRODUCTS P2 WHERE P2.id_p = P.id_p AND deletionDate IS NULL)
										AND EXISTS(SELECT * FROM COMPONENTS C WHERE name LIKE ? AND C.deletionVersion IS NULL AND C.id_p = P.id_p AND C.version <= P.version)
										AND EXISTS(SELECT * FROM COMPONENTS C WHERE name LIKE ? AND C.deletionVersion IS NULL AND C.id_p = P.id_p AND C.version <= P.version)
										AND EXISTS(SELECT * FROM COMPONENTS C WHERE name LIKE ? AND C.deletionVersion IS NULL AND C.id_p = P.id_p AND C.version <= P.version)
												');
							$req->execute(array('%'.$w.'%', '%'.$f1.'%', '%'.$f2.'%', '%'.$f3.'%'));
							$d = $req->fetch();
							echo '<h2> '.min($d[0],30).' résultat(s)</h2>';
							$req = $bdd->prepare('SELECT * FROM PRODUCTS P WHERE P.wording LIKE ? AND P.version = (SELECT MAX(version) FROM PRODUCTS P2 WHERE P2.id_p = P.id_p AND deletionDate IS NULL)
										AND EXISTS(SELECT * FROM COMPONENTS C WHERE name LIKE ? AND C.deletionVersion IS NULL AND C.id_p = P.id_p AND C.version <= P.version)
										AND EXISTS(SELECT * FROM COMPONENTS C WHERE name LIKE ? AND C.deletionVersion IS NULL AND C.id_p = P.id_p AND C.version <= P.version)
										AND EXISTS(SELECT * FROM COMPONENTS C WHERE name LIKE ? AND C.deletionVersion IS NULL AND C.id_p = P.id_p AND C.version <= P.version)
										ORDER BY creationDate DESC LIMIT 30	');
							$req->execute(array('%'.$w.'%', '%'.$f1.'%', '%'.$f2.'%', '%'.$f3.'%'));
						}
						else {
							$req = $bdd->prepare('SELECT COUNT(*) FROM PRODUCTS P WHERE P.wording LIKE ? AND P.version = (SELECT MAX(version) FROM PRODUCTS P2 WHERE P2.id_p = P.id_p AND deletionDate IS NULL)');
							$req->execute(array('%'.$w.'%'));
							$d = $req->fetch();
							echo '<h2> '.min($d[0],30).' résultat(s)</h2>';
							$req = $bdd->prepare('SELECT * FROM PRODUCTS P WHERE P.wording LIKE ? AND P.version = (SELECT MAX(version) FROM PRODUCTS P2 WHERE P2.id_p = P.id_p AND deletionDate IS NULL) ORDER BY creationDate DESC LIMIT 30');
							$req->execute(array('%'.$w.'%'));
						}
						while($data = $req->fetch()){
							echo '
							<a href="product.php?id='.$data['id_p'].'">
								<div class="product">
									<div class="productWording">'.escape($data['wording']).'</div>
									Modifié : '.$data['creationDate'].'<br>
									Par : '.escape($data['pseudo']).'
								</div>
							</a>';
						}
					}
					else{			//Sinon
						echo '<h2>Derniers produits</h2>';
						$req = $bdd->prepare('SELECT * FROM PRODUCTS P1 WHERE P1.version = (SELECT MAX(P2.version) FROM PRODUCTS P2 WHERE P2.id_p = P1.id_p AND P2.deletionDate IS NULL) ORDER BY P1.creationDate DESC LIMIT 30');
						$req->execute();
						while($data = $req->fetch()){
							echo '
							<a href="product.php?id='.$data['id_p'].'">
								<div class="product">
									<div class="productWording">'.escape($data['wording']).'</div>
									Modifié : '.$data['creationDate'].'<br>
									Par : '.escape($data['pseudo']).'
								</div>
							</a>';
						}
					}
					?>

				</div>
				<div id="m_connection">
					<h2>Se connecter</h2>
					<form action="home.php" method="post">
						<input type="text" name="cLogin" placeholder="Pseudo" maxlength="12" required />
						<input type="password" name="cPassword" placeholder="Mot de passe" maxlength="15" required />
						<input type="submit" name="connection" value="Connection" />
					</form>
					<?php if (isset($errorConnection)) echo $errorConnection; ?>
				</div>
				<div id="m_inscription">
					<h2>Créer un compte</h2>
					<form action="home.php" method="post">
						<input type="text" name="iLogin" placeholder="Pseudo" maxlength="12" required />
						<input type="password" name="iPassword" placeholder="Mot de passe" maxlength="15" required />
						<input type="password" name="iConfirm" placeholder="Confirmer le mot de passe" maxlength="15" required />
						<input id="check" type="checkbox" name="regagree" value="valeur" /> Je certifie avoir pris connaissance du règlement
						<input type="submit" name="validation" value="Valider" disabled />
					</form>
					<?php if (isset($errorInscription)) echo $errorInscription; ?>
				</div>
			</div>
		</div>		<script type="text/javascript" src="../js/home.js"></script>
		<?php require_once("redirection.php"); ?>
	</body>
</html>
