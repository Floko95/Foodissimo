var i = 3;	
var table = $("#addTable");
function addField() {
	i++;
	table.append($('<tr id="'+i+'"><td><input type="text" name="component[]" placeholder="vide"/></td><td><input type="text" name="quantity[]" /></td></tr>'));
	 

}

function removeField(){
	if (i > 1){
		$("#"+i).remove();
	}
	i--;
}


document.getElementById("addComponentButton").addEventListener("click", addField);
document.getElementById("removeComponentButton").addEventListener("click", removeField);