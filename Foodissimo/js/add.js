var i = 3;	
var table = document.getElementById("addTable");
function addField() {
	i++;
	table.innerHTML += '<tr id="'+i+'"><td><input type="text" name="name[]" placeholder="vide" /></td>' + '<td><input type="text" name="quantity[]" placeholder="0" /></td></tr>';

}

function removeField(){
	if (i > 1){
		table.removeChild(document.getElementById(i));
	}
	i--;
}


document.getElementById("addComponentButton").addEventListener("click", addField);
document.getElementById("removeComponentButton").addEventListener("click", removeField);