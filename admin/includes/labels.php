<?php
	$member_label = (Configuration::getValue('member_label')) ? Configuration::getValue('member_label') : 'Member';
	define('MEMBER_LABEL',$member_label);
	$sparepart_label = (Configuration::getValue('spare_part')) ? Configuration::getValue('spare_part') : 'Spare part';
	define('SPAREPART_LABEL',$sparepart_label);
	$sparepart_label = (Configuration::getValue('supply_label')) ? Configuration::getValue('supply_label') : 'Supply';
	define('SUPPLY_LABEL',$sparepart_label);
	$transfer_label = (Configuration::getValue('transfer_inv_label')) ? Configuration::getValue('transfer_inv_label') : 'Transfer Inventory';
	define('TRANSFER_LABEL',$transfer_label);
	$rec_inv_label = (Configuration::getValue('receive_inv_label')) ? Configuration::getValue('receive_inv_label') : 'Receive Inventory';
	define('REC_INV_LABEL',$rec_inv_label);
	$invoice_label = (Configuration::getValue('invoice_label')) ? Configuration::getValue('invoice_label') : 'Invoice';
	define('INVOICE_LABEL',$invoice_label);
	$dr_label = (Configuration::getValue('dr_label')) ? Configuration::getValue('dr_label') : 'DR';
	define('DR_LABEL',$dr_label);
	$pr_label = (Configuration::getValue('pr_label')) ? Configuration::getValue('pr_label') : 'PR';
	define('PR_LABEL',$pr_label);
	$incomplete_label = (Configuration::getValue('inc_lbl')) ? Configuration::getValue('inc_lbl') : 'Incomplete';
	define('INCOMPLETE_LABEL',$incomplete_label);
	$damage_label = (Configuration::getValue('damage_lbl')) ? Configuration::getValue('damage_lbl') : 'Damage';
	define('DAMAGE_LABEL',$damage_label);
	$missing_label = (Configuration::getValue('MISSING_LABEL')) ? Configuration::getValue('MISSING_LABEL') : 'Missing';
	define('MISSING_LABEL',$missing_label);
	$invoice_prefix = (Configuration::getValue('invpref_label')) ? Configuration::getValue('invpref_label') : '';
	define('INVOICE_PREFIX',$invoice_prefix);
	$dr_prefix = (Configuration::getValue('drpref_label')) ? Configuration::getValue('drpref_label') : '';
	define('DR_PREFIX',$dr_prefix);
	$pr_prefix = (Configuration::getValue('prpref_label')) ? Configuration::getValue('prpref_label') : '';
	define('PR_PREFIX',$pr_prefix);

	$other_issue_lbl = (Configuration::getValue('other_issue_lbl')) ? Configuration::getValue('other_issue_lbl') : '';
	define('OTHER_ISSUE_LABEL',$other_issue_lbl);