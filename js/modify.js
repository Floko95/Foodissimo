var i = 1;	
var table = $("#addTable");
function addField() {
	i++;
	table.append($('<tr id="'+i+'"><td><input type="text" name="newComponent[]" placeholder="vide" /></td><td><input type="text" name="newQuantity[]" /></td></tr>'));
	 

}

function removeField(){
	if (i > 1){
		$("#"+i).remove();
	}
	i--;
}

document.getElementById("addComponentButton").addEventListener("click", addField);
document.getElementById("removeComponentButton").addEventListener("click", removeField);