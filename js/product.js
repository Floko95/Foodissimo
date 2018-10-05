// Supprimer une modification (en appuyant sur la poubelle)
$("button[id*='deleteButt']").bind('click', function() {
	if (confirm('Voulez-vous vraiment effacer cette modification ?')) {
		var location = "processing.php?";
		document.location.href='processing.php?id_p='+this.dataset.id_p+'&version='+this.dataset.version;
	}
});

// Effectuer un rollback de l'application à partir d'une modification (boutton flèche)
$("button[id*='rollbackButt']").bind('click', function() {
	if (confirm('Voulez-vous vraiment revenir à cette date ?')) {
		document.location.href='processing.php?date='+this.dataset.date;
	}
});

//Bannir un utilisateur (boutton gyrophare)
$("button[id*='banButt']").bind('click', function() {
	if (confirm('Voulez-vous vraiment bannir cet utilisateur ?')) {
		document.location.href='processing.php?pseudo='+this.dataset.pseudo;
	}
});

if (document.getElementById("modifyButton") != null){
	document.getElementById("modifyButton").addEventListener("click", function(){
		document.location.href='modify.php?id='+this.dataset.id_p;
	});
}
