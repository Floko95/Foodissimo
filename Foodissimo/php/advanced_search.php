<?php

if(isset($_POST['champ_recherche']))//Attention ,le nom "champ_recherche[] doit etre le nom donné à chaque input de recherche"
{

	if(count($_POST['champ_recherche'])==0)
	{
		echo"<p>Erreur de champs de recherche: aucun post n'a été transmis</p>";
	}

	else
	{

		$champs = $_POST['champ_recherche'];

		$req=$bdd->prepare("CREATE view Recherche as SELECT * from PRODUCTS WHERE id_p in(SELECT id_p FROM COMPONENTS WHERE name = :n)");//on prend le premier champ et recherche les produits qui ont cet ingrédient
		$req->bindValue(':n',$champs[0]);
		$req->execute();


		if(count($_POST['champ_recherche'])!= 1)//si cet ingrédient ne vient pas seul
		{

			foreach($champs as $n)//on repart du premier champ, qui devrait normalement donner la meme vue
			{
				$req=$bdd->prepare("REPLACE view Recherche as SELECT * from Recherche WHERE id_p in(SELECT id_p FROM COMPONENTS WHERE name = :n)");//on recherche dans la vue les produits avec l'ingrédient n
				$req->bindValue(':n',$n);
				$req->execute();
			}
		}
	}
	//maintenant que la vue est complete:
	$req=$bdd->prepare("SELECT * from Recherche");//on prend tous les produits trouvés
	$req->execute();
	while($ligne=$req->fetch(PDO::FETCH_ASSOC))//pour chaque produit on rajoute une case
		{
				echo '<a href="product.html?id='.$ligne['wording'].'"><div class="product"></div></a>';//normalement, la case devrait se rajouter apres les autres grace à l'emplacment de l'appel de ce script
		}
	$req=$bdd->prepare("DELETE view Recherche");
	$req->execute();
}
?>
