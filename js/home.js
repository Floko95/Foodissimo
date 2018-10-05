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

if (document.getElementById("b_home") != null){
	document.getElementById("b_home").addEventListener("click", function(){changeTab('m_home');});
}
if (document.getElementById("b_connection") != null){
	document.getElementById("b_connection").addEventListener("click", function(){changeTab('m_connection');});
}
if (document.getElementById("b_inscription") != null){
	document.getElementById("b_inscription").addEventListener("click", function(){changeTab('m_inscription');});
}
if (document.getElementById("check") != null){
	document.getElementById("check").addEventListener("click", function(){changeState(this.form);});
}

document.getElementById("hamburger").addEventListener("click", function(){
	var left = document.getElementById("left");
	var right = document.getElementById("right");
	if(left.style.display == 'none'){
		left.style.display = 'block';
		right.style.width = 'calc(100% - 300px)';
	}
	else{
		left.style.display = 'none';
		right.style.width = '100%';
	}
});

