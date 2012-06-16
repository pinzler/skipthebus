<?php include "header.php"; ?>


<div id="forms">
<form name="form1" method="post" action="register.php">
<table>
	<tr>
		<td colspan="3"><strong>Register </strong></td>
	</tr>
	<tr>
		<td width="78">E-mail</td>
		<td width="6">:</td>
		<td width="294"><input name="email" type="text" id="email"></td>
	</tr>
	<tr>
		<td width="78">Mobile Phone</td>
		<td width="6">:</td>
		<td width="294"><input name="phone" type="text" id="phone"></td>
	</tr>
	<tr>
		<td>Password</td>
		<td>:</td>
		<td><input name="password" type="password" id="password"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><input type="submit" name="Submit" value="Register"></td>
	</tr>
</table>
</form>


<form name="form2" method="post" action="checklogin.php">
<table>
	<tr>
		<td colspan="3"><strong>Member Login </strong></td>
	</tr>
	<tr>
		<td width="78">E-mail</td>
		<td width="6">:</td>
		<td width="294"><input name="email" type="text" id="email"></td>
	</tr>
	<tr>
		<td>Password</td>
		<td>:</td>
		<td><input name="password" type="password" id="password"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><input type="submit" name="Submit" value="Login"></td>
	</tr>
</table>
</form>
</div>

<?php include "footer.php"; ?>

