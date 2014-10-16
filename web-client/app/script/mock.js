var listings = [ { 
	id: 0,
	title: 'Test Listing',
	owner: 'admin',
	description: 'This is just a test listing returned by the mock server.',
	imageUrls: [],
	}
];

var users = {
	'test@gatech.edu': {
		email: 'test@gatech.edu',
		password: 'password'
	},
	'other@gatech.edu': {
		email: 'other@gatech.edu',
		password: 'password'
	}
};

var tokens = {};

function userForToken(token) {
	if (!token) return null;
	var email = tokens[token];
	if (!email) return null;
	return users[email];
};

app.run(function($httpBackend) {
	// Get an authorization token
	$httpBackend.whenPOST(/api\/auth/).respond(function(method, url, data) {
		var authObject = angular.fromJson(data);
		var user = users[authObject.email];
		if (!user || authObject.password !== user.password) {
			return [403, token, {}];
		}
		
		var token = Object.keys(tokens).length;
		tokens[token] = user.email;
		
		return [200, token, {}];
	});
	
	// Update the authenticated users password
	$httpBackend.whenPUT(/api\/user/).respond(function(method, url, data, headers) {
		var user = userForToken(headers['X-SessionId']);
		if (!user) {
			return [401, null, {}];
		}
		
		var userChanges = angular.fromJson(data);
		if (userChanges.password === user.password) {
			user.password = userChanges.password;
			return [200, null, {}];
		} else {
			return [401, null, {}];
		}
	});
	
	// Delete the authenticated user
	$httpBackend.whenDELETE(/api\/auth/).respond(function(method, url, data, headers) {
		var user = userForToken(headers['X-SessionId']);
		if (!user) {
			return [401, null, {}];
		}

		var authorization = angular.fromJson(data);
		if (authorization.password === user.password) {
			delete users[user.email];
			return [200, null, {}];
		} else {
			return [401, null, {}];
		}
	});
	
	// Get all listings
	$httpBackend.whenGET(/api\/listings/).respond(function(method, url, data, headers) {
		var user = userForToken(headers['X-SessionId']);
		if (!user) {
			return [401, null, {}];
		}
		
		return [200, listings, {}];
	});
	
	// Post a listing
	$httpBackend.whenPOST(/api\/listings/).respond(function(method, url, data, headers) {
		var user = userForToken(headers['X-SessionId']);
		if (!user) {
			return [401, null, {}];
		}
		
		var owner = tokens[token];
		var listing = angular.fromJson(data);

		listing.id = listings.length;
		listing.owner = owner;
		
		listings.push(listing);
		
		return [200, listing, {}];
	});
	
	// Update a listing
	$httpBackend.whenPUT(/api\/listings\/.*/).respond(function(method, url, data, headers) {
		var user = userForToken(headers['X-SessionId']);
		if (!user) {
			return [401, null, {}];
		}
		
		var id = url.split('/').pop();
		if (listings[id]) {
			if (listings[id].owner === tokens[token]) {
				var modifications = angular.fromJson(data);
				if (modifications.title) {
					listings[id].title = modifications.title;
				}
				if (modifications.category) {
					listings[id].category = modifications.category;
				}
				if (modifications.askingPrice) {
					listings[id].askingPrice = modifications.askingPrice;
				}
				if (modifications.description) {
					listings[id].description = modifications.description;
				}
				if (modifications.images) {
					listings[id].title = modifications.images;
				}

				return [200, null, {}];
			} else {
				return [403, null, {}];
			}
		} else {
			return [404, null, {}];
		}
	});
	
	// Delete a listing
	$httpBackend.whenDELETE(/api\/listings\/.*/).respond(function(method, url, data, headers) {
		var user = userForToken(headers['X-SessionId']);
		if (!user) {
			return [401, null, {}];
		}
		
		var id = url.split('/').pop();
		if (listings[id]) {
			if (listings[id].owner === tokens[token]) {
				listings.splice(id, 1);
				return [200, null, {}];
			} else {
				return [403, null, {}];
			}
		} else {
			return [404, null, {}];
		}
	});
	
	$httpBackend.whenGET(/partials\/.*/).passThrough();
});

