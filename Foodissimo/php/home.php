<!DOCTYPE html>
<html>
	<head>
		<title>Projet</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/design.css">
		<link rel="stylesheet" type="text/css" href="../css/nav.css">
		<link rel="stylesheet" type="text/css" href="../css/form.css">
	</head>
	<body>

		<nav>
			<ul>
				<li><a href="home.html">Accueil</a></li>
				<li><a href="" class="red"><img src="../img/chess-queen.png">Gérer</a></li>
				<li><a href="add.html">Ajouter un produit</a></li>
				<li><a href="search.html">Rechercher un produit</a></li>
				<li><button id="disconnect"></li>
			</ul>
		</nav>
		<div id="all">
			<div id="menu">
				<ul>
					<li><button id="b_home">Accueil</button></li>
					<li><button id="b_connection">Se connecter</button></li>
					<li><button id="b_inscription">Créer un compte</button></li>
				</ul>
			</div>
			<div id="content">
				<div id="m_home">
					<h1>Liste de produits</h1>
					<a href="product.php"><div class="product">Pizza 4 fromages - Tomates (..%), Fromage (..%)</div></a>
					<a href="product.php"><div class="product"></div></a>
					<a href="product.php"><div class="product"></div></a>
					<a href="product.php"><div class="product"></div></a>
					<a href="product.php"><div class="product"></div></a>
					<a href="product.php"><div class="product"></div></a>
					<?php require_once("advanced_search.php"); ?>
				</div>
				<div id="m_connection">
					<h1>Se connecter</h1>
					<form action="home.php">
						<input type="text" name="field1" placeholder="Pseudo" required />
						<input type="password" name="field2" placeholder="Mot de passe" />
						<input type="submit" value="Connexion" />
					</form>
				</div>
				<div id="m_inscription">
					<h1>Créer un compte</h1>
					<form action="home.php" methed="post">
						<input type="text" name="field1" placeholder="Pseudo" />
						<input type="password" name="field2" placeholder="Mot de passe" />
						<input type="password" name="field3" placeholder="Confirmer le mot de passe" />
						<input id="check" type="checkbox" name="regagree" value="valeur" /> Je certifie avoir pris connaissance du règlement						<input type="submit" name="validation" value="Valider" disabled />
					</form>
				</div>
			</div>
		</div>		<script type="text/javascript" src="../js/home.js"></script>
	</body>
</html>
