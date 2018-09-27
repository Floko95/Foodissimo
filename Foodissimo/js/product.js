function removeModification(id){
	if (confirm('Voulez-vous vraiment effacer cette modification ?')) {
    // Save it!
	}
	else {
    // Do nothing!
	}
}

function banUser(id){
	
}

function rollback(id){
	
}


$("p[id*='fo']").bind('click', function() {
	if (confirm('Voulez-vous vraiment effacer cette modification ?')) {
    // Save it!
		document.location.href='processing.php?id_p='+this.dataset.id_p;
	}
	else {
    // Do nothing!
	}
});
