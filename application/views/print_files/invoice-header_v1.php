<?php //print_r($invoice);?>
<table>
    <tr>
	
        <td class="myco">
            <img src="<?php $loc = location($invoice['loc']);  echo FCPATH . 'userfiles/company/' . $loc['logo'] ?>" class="top_logo" height="70px">
        </td>
      
		
		<td class="mycoss" colspan="2">
<h2>Thok ki Dukaan Online Stores</h2>
<p>Deals in: All kind of Exotic Fruits and Indian Fresh Fruits & Vegetables</p>
<p> C 29 Niranjan pur mandi
Dehradun, Uttarakhand
India - 248002</p>
        </td>
		<td class="myw">
		<h2><?= $general['title'] ?></h2>
        </td>

    </tr>
</table>
<br>