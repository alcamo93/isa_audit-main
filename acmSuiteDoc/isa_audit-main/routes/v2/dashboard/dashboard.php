<?php

use App\Http\Controllers\V2\Dashboard\DashboardController;
use App\Http\Controllers\V2\Dashboard\ReportDashboardController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
	Route::controller(DashboardController::class)->group(function () {
		// views
		Route::get('dashboard/customers/view', 'showCustomersView')->name('dashboard.customers.view');
		Route::get('dashboard/customers/{idCustomer}/view', 'showCustomerView')->name('dashboard.customer.view');
		Route::get('dashboard/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligation/{obligationRegisterId}/view', 'showCorporateObligationView')->name('dashboard.corporate.obligation.view');
		Route::get('dashboard/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/view', 'showCorporateAuditView')->name('dashboard.corporate.audit.view');
		Route::get('dashboard/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/compliance/{idAuditRegister}/view', 'showCorporateComplianceView')->name('dashboard.corporate.compliance.view');
		Route::get('dashboard/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/all/view', 'showCorporateAllView')->name('dashboard.corporate.all.view');
		// views data
		Route::get('dashboard/customers', 'getCustomers')->name('dashboard.customers');
		Route::get('dashboard/corporates', 'getCorporatesByCustomer')->name('dashboard.customer.corporates');
		Route::get('dashboard/corporate/only', 'getCorporates')->name('dashboard.customer.corporates.only');
		// globals
		Route::get('dashboard/customers/{idCustomer}/obligations', 'getDataObligationCustomer')->name('dashboard.customers.data.obligations');
		Route::get('dashboard/customers/{idCustomer}/obligations/historical', 'getDataHistoricalCustomer')->name('dashboard.customers.data.historicals');
		Route::get('dashboard/customers/{idCustomer}/audit', 'getDataAuditCustomer')->name('dashboard.customers.data.audits');
		Route::get('dashboard/customers/{idCustomer}/audit/historical', 'getDataAuditHistoricalCustomer')->name('dashboard.customers.data.audits.historicals');
		Route::get('dashboard/customers/{idCustomer}/compliance', 'getDataComplianceCustomer')->name('dashboard.customers.data.compliance');
		Route::get('dashboard/customers/{idCustomer}/compliance/historical', 'getDataComplianceHistoricalCustomer')->name('dashboard.customers.data.compliance.historicals');
		// corporates
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/all', 'getAllDataCorporate')->name('dashboard.corporate.data.all');
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligations', 'getDataObligationCorporate')->name('dashboard.corporate.data.obligations');
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligations/actions', 'getDataObligationActionCorporate')->name('dashboard.corporate.data.obligation.actions');
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}', 'getDataAuditCorporate')->name('dashboard.corporate.data.audit');
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/actions', 'getDataAuditActionCorporate')->name('dashboard.corporate.data.audit.actions');
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/compliance/{idAuditRegister}', 'getDataComplianceCorporate')->name('dashboard.corporate.data.compliance');
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/compliance/{idAuditRegister}/actions', 'getDataComplianceActionCorporate')->name('dashboard.corporate.data.compliance.actions');
		// records
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/obligation/{idObligationRegister}/record', 'getRecordsDashboardObligation')->name('dashboard.corporate.data.obligation.record');
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/audit/{idAuditRegister}/record', 'getRecordsDashboardAudit')->name('dashboard.corporate.data.audit.record');
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/compliance/{idAuditRegister}/record', 'getRecordsDashboardCompliance')->name('dashboard.corporate.data.compliance.record');
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/{section}/{idSectionRegister}/action/{idActionRegister}/record', 'getRecordsDashboardActionPlan')->name('dashboard.corporate.data.section.action.record');
	});
});

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
	Route::controller(ReportDashboardController::class)->group(function () {
		// customer
		Route::get('dashboard/customers/{idCustomer}/report/obligation', 'getReportObligationCustomer')->name('dashboard.customers.report.data.obligation');
		Route::get('dashboard/customers/{idCustomer}/report/audit', 'getReportAuditCustomer')->name('dashboard.customers.report.data.audit');
		Route::get('dashboard/customers/{idCustomer}/report/compliance', 'getReportComplianceCustomer')->name('dashboard.customers.report.data.compliance');
		// corporate
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/report/obligation/{idObligationRegister}', 'getReportObligationCorporate')->name('dashboard.corporate.report.data.obligations');
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/report/audit/{idAuditRegister}', 'getReportAuditCorporate')->name('dashboard.corporate.report.data.audit');
		Route::get('dashboard/corporate/project/{idAuditProcess}/applicability/{idAplicabilityRegister}/report/compliance/{idAuditRegister}', 'getReportComplianceCorporate')->name('dashboard.corporate.report.data.compliance');
	});
});
