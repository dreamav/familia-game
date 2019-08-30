<?
$mail->Body = <<<MEVINSIDE
<br>
<table>
	<tbody>
		<tr>
			<td style="text-align:left; padding:3px 7px;"><strong>Email</strong></td>
			<td style="text-align:left; padding:3px 7px;"><strong>Сообщение</strong></td>
			<td style="text-align:left; padding:3px 7px;"><strong>ID пользователя</strong></td>
		</tr>
		<tr>
			<td style="text-align:left; padding:3px 7px;">{$data["contact"]}</td>
			<td style="text-align:left; padding:3px 7px;">{$data["message"]}</td>
			<td style="text-align:left; padding:3px 7px;">{$data["u_id"]}</td>
		</tr>
	</tbody>
</table>
MEVINSIDE;

?>