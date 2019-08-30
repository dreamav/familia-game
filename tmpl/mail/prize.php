<?
$mail->Body = <<<MEVINSIDE
<br>
<table>
	<tbody>
		<tr>
			<td style="text-align:left; padding:3px 7px;"><strong>Email</strong></td>
			<td style="text-align:left; padding:3px 7px;"><strong>user ID</strong></td>
			<td style="text-align:left; padding:3px 7px;"><strong>Номер сертификата</strong></td>
			<td style="text-align:left; padding:3px 7px;"><strong>Аккаунт в соц сети</strong></td>
		</tr>
		<tr>
			<td style="text-align:left; padding:3px 7px;">{$data["prize_request"]}</td>
			<td style="text-align:left; padding:3px 7px;">{$_SESSION['user']->id}</td>
			<td style="text-align:left; padding:3px 7px;">{$_SESSION['user']->cert_no}</td>
			<td style="text-align:left; padding:3px 7px;"><a href="{$_SESSION['user']->socialPage}">{$_SESSION['user']->provider}</a></td>
		</tr>
	</tbody>
</table>
MEVINSIDE;

?>