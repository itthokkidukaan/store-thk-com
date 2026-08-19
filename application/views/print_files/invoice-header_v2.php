<table>

    <tr>
	  <td class="myco">
            <img src="<?php $loc = invoice_company_details($invoice['loc']);  echo FCPATH . $loc['logo_path'] ?>" class="top_logo" height="70px">
        </td>





		<td class="mycoss">
<?php
$company_addr_bits = array_filter([trim((string)$loc['address']), trim((string)$loc['city']), trim((string)$loc['region'])]);
$company_addr = implode(', ', $company_addr_bits);
$company_country_bits = array_filter([trim((string)$loc['country']), trim((string)$loc['postbox'])]);
$company_country = implode(', ', $company_country_bits);
?>
<h1 style="text-align: center; color:red"><?= htmlspecialchars($loc['cname']) ?></h1> <br>
<?php if ($company_addr !== ''): ?><p><?= htmlspecialchars($company_addr) ?></p><?php endif; ?>
<?php if ($company_country !== ''): ?><p><?= htmlspecialchars($company_country) ?></p><?php endif; ?>
        </td>
		

    </tr>
</table>
<br>