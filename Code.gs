// Sheet: https://docs.google.com/spreadsheets/d/1PQJMqib3gKQ-ExgXs2TqfgqGbCHAzHx9B8u6qVs8gxI
var SHEET_ID = '1PQJMqib3gKQ-ExgXs2TqfgqGbCHAzHx9B8u6qVs8gxI';

function doGet(e) {
  return saveRow(getParams(e));
}

function doPost(e) {
  return saveRow(getParams(e));
}

function getParams(e) {
  if (e && e.parameter) {
    return e.parameter;
  }
  return {};
}

// Run THIS from the editor (not doPost) to test + authorize
function testSave() {
  var result = saveRow({
    submitted_at: '13/06/2026, 01:00:00',
    name: 'Test User',
    mobile: '1234567890',
    email: 'test@test.com',
    service: 'test service'
  });
  Logger.log(result.getContent());
}

function saveRow(p) {
  p = p || {};
  var submittedAt = String(p.submitted_at || '').trim();
  var name = String(p.name || '').trim();
  var mobile = String(p.mobile || '').trim();
  var email = String(p.email || '').trim();
  var service = String(p.service || '').trim();

  if (!name || !mobile || !email || !service) {
    return jsonOut({ success: false, message: 'All fields are required' });
  }
  if (!/^[0-9]{10}$/.test(mobile)) {
    return jsonOut({ success: false, message: 'Mobile number must be 10 digits' });
  }

  var sheet = SpreadsheetApp.openById(SHEET_ID).getSheets()[0];

  if (sheet.getLastRow() === 0) {
    sheet.appendRow(['submitted_at', 'name', 'mobile', 'email', 'service']);
  }

  sheet.appendRow([submittedAt, name, mobile, email, service]);

  return jsonOut({ success: true, message: 'Saved to Google Sheet' });
}

function jsonOut(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj))
    .setMimeType(ContentService.MimeType.JSON);
}
