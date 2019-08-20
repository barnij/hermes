<script type="text/javascript">
	function zamianafooter()
	{
		if(document.getElementById("footerleft").innerHTML == "Autorstwa Bartosza Jaśkiewicza dla")
			document.getElementById("footerleft").innerHTML = "I Liceum Ogólnokształcące im. Tadeusza Kościuszki w Legnicy";
		else
			document.getElementById("footerleft").innerHTML = "Autorstwa Bartosza Jaśkiewicza dla";
	}
	
</script>

<?php
echo '	<footer>
			<div id="footer">
				<table>
					<th id="footerleft" style="text-align: left; padding-left: 20px;" onclick="zamianafooter()">
						I Liceum Ogólnokształcące im. Tadeusza Kościuszki w Legnicy
					</th>
					<th style="text-align: right; padding-right: 30px;">
						&copy; Wszelkie prawa zastrzeżone
					</th>
				</table>
			</div>
		</footer>';
?>