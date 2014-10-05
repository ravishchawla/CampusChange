var items = [
{'name': 'Nexus S',
'summary': 'Fast just got faster with Nexus S.'},
{'name': 'Motorola XOOM™ with Wi-Fi',
'summary': 'The Next, Next Generation tablet.'},
{'name': 'MOTOROLA XOOM™',
'summary': 'The Next, Next Generation tablet.'},
{'name': 'Nexus S',
'summary': 'Fast just got faster with Nexus S.'},
{'name': 'Motorola XOOM™ with Wi-Fi',
'summary': 'The Next, Next Generation tablet.'},
{'name': 'MOTOROLA XOOM™',
'summary': 'The Next, Next Generation tablet.'},
{'name': 'Nexus S',
'summary': 'Fast just got faster with Nexus S.'},
{'name': 'Motorola XOOM™ with Wi-Fi',
'summary': 'The Next, Next Generation tablet.'},
{'name': 'MOTOROLA XOOM™',
'summary': 'The Next, Next Generation tablet.'},
{'name': 'Nexus S',
'summary': 'Fast just got faster with Nexus S.'},
{'name': 'Motorola XOOM™ with Wi-Fi',
'summary': 'The Next, Next Generation tablet.'},
{'name': 'MOTOROLA XOOM™',
'summary': 'The Next, Next Generation tablet.'},
{'name': 'Nexus S',
'summary': 'Fast just got faster with Nexus S.'},
{'name': 'Motorola XOOM™ with Wi-Fi',
'summary': 'The Next, Next Generation tablet.'},
{'name': 'MOTOROLA XOOM™',
'summary': 'The Next, Next Generation tablet.'},
];

app.run(function($httpBackend) {
	$httpBackend.whenGET(/thrift.php\?action=auth.*/).respond(function(method, url, data) {
		return [200, 'token', {}];
	});
	$httpBackend.whenGET(/thrift.php\?action=list_items.*/).respond(function(method, url, data) {
		return [200, 'token', {}];
	});
	$httpBackend.whenGET(/partials\/.*/).passThrough();
});