<?php
if(isset($_POST['connection']) && $_POST['connection'] == 'Connection'){
		if(isset($_POST['cLogin'], $_POST['cPassword']) && !empty($_POST['cLogin']) && !empty($_POST['cPassword']))
		{
			echo "<script>changeTab('m_connection');</script>";
		}
}
if (isset($_POST['validation']) && $_POST['validation'] == 'Valider') { 
	if ((isset($_POST['iLogin']) && !empty($_POST['iLogin'])) && (isset($_POST['iPassword']) && !empty($_POST['iPassword'])) && (isset($_POST['iConfirm']) && !empty($_POST['iConfirm']))) { echo "<script>changeTab('m_inscription');</script>";}
}
?>