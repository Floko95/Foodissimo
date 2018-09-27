
function changeState(form) {
	if(form.regagree.checked == true) {form.validation.disabled = false }
	if(form.regagree.checked == false) {form.validation.disabled = true }
}

var tab = 'm_home';

function changeTab(name){

	document.getElementById(tab).style.display = 'none';
	document.getElementById(name).style.display = 'block';
	tab = name;
}

document.getElementById("b_home").addEventListener("click", function(){changeTab('m_home');});
document.getElementById("b_connection").addEventListener("click", function(){changeTab('m_connection');});
document.getElementById("b_inscription").addEventListener("click", function(){changeTab('m_inscription');});
document.getElementById("check").addEventListener("click", function(){changeState(this.form);});

