<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("КРЕДИТ");
?><form id="request-order-form" action="add_form_result.php" method="post">
<div>
<h2>Получить кредит</h2>

<div class="controls">
<label>Ф.И.О.:</label>
<div class="input"> <span class="required"></span>

<input type="text" name="user" id="form-user-name">
</div>
</div>
<div class="controls">
<label>Сумма кредита:</label>
<div class="input"> <span class="required"></span>

<input type="text" name="credit" id="form-user-credit">
</div>
</div>
<div class="controls">
<label>E-Mail:</label>
<div class="input"> 

<input type="text" name="mail" id="form-user-email">
</div>
</div>
</div>
<div class="actions">
<br>
<button type="submit" class="btn">Отправить</button>
</div>
</form>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>