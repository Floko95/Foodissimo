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
if (!isset($_SESSION['pseudo'])) { 
   header ('Location: index.php'); 
   exit();  
}  
?> 

<?php		// Inscription
if (isset($_POST['inscription']) && $_POST['inscription'] == 'Inscription') { 
	if ((isset($_POST['login']) && !empty($_POST['login'])) && (isset($_POST['password']) && !empty($_POST['password'])) && (isset($_POST['password_confirm']) && !empty($_POST['password_confirm']))) { 
		if ($_POST['password'] != $_POST['pass_confirm']) { 
			$erreur_inscription = 'Les 2 mots de passe sont diff&eacute;rents.'; 
		} 
		else { 
			$req = $bdd->prepare('SELECT count(*) FROM members WHERE login = ?');
			$req->execute(array($_POST['login']));
			$data = $req->fetch();
			if ($data[0] == 0) { 
				$req = $bdd->prepare('INSERT INTO members(login, password, banUntil) VALUES(:login, :password, CURDATE())');
				$req->execute(array(
				'login' => $_POST['login'],
				'password' => sha1($_POST['password'])));
				session_start(); 
				$_SESSION['login'] = $_POST['login'];
				$req = $bdd->prepare('SELECT * FROM members WHERE login = ?');
				$req->execute(array($_SESSION['login']));
				$donnees = $req->fetch();
				$_SESSION['id'] = $donnees['id'];
				$_SESSION['admin'] = $donnees['admin'];
				header('Location: home.php'); 
				exit(); 
			} 
			else { 
			$erreur_inscription = 'Un membre poss&egrave;de déjà ce login.'; 
			} 
		} 
	} 
	else { 
		$erreur_inscription = 'Au moins un des champs est vide.'; 
	}  
}  
?>

<?php		// Connexion
	if(isset($_POST['username2'], $_POST['password3']))  //On verifie si le formulaire a ete envoye
	{
		$username = $_POST['username2'];
		$password = sha1($_POST['password3']);
		//On recupere le mot de passe de l'utilisateur
		$req = $bdd->prepare('SELECT mdp,id from joueurs where pseudo = ?');
		$req->execute(array($username));
		$data = $req->fetch();
		//On le compare a celui qu'il a entre et on verifie si le membre existe
		if($data['mdp']==$password)
		{
			session_start(); 
			$_SESSION['pseudo'] = $username;
			$req = $bdd->prepare('SELECT * FROM joueurs WHERE pseudo = ?');
			$req->execute(array($_SESSION['pseudo']));
			$donnees = $req->fetch();
			$_SESSION['id'] = $donnees['id'];
			$_SESSION['statut'] = $donnees['statut'];
			header('Location: home.php'); 
			exit(); 
		}
		else
		{
			$erreur_connexion = 'Pseudo ou mot de passe incorrect !';
		}
	}
?>