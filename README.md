# Campus Exchange

Specific component instructions in subdirectories. Backend services in `service/` and web client in `web-client/`.

### Service Setup

#### Dependencies

* [apache httpd](http://httpd.apache.org)
* [php](http://php.net)

### Web Client Setup

#### Dependencies

* [Node.js](http://nodejs.org)
* [npm](https://www.npmjs.org)

#### Installation

```sh
$ cd web-client
$ npm install -g gulp bower
$ npm install
$ bower install
```

##### Ubuntu Note
The gulp and bower scripts use the ```node``` command to indirectly start ```nodejs```. But on Ubuntu ```node``` does something else. To fix change ```node``` to ```nodejs``` at the start of the gulp and bower scripts (probably in ```/usr/local/bin/```).

#### Application Start

```sh
$ gulp debug
```

Point browser to [http://localhost:8000](http://localhost:8000)

#REST API Documentation

Format:
{title}
{HTTP METHOD} {endpoint}
{optional headers}
{json body}

{returns}

all requests
501 - server error
400 - missing parameters in json request

Test call
GET /api/hello/name
(success) Hello, name!


Authoritzation


Get an authorization token
POST /api/auth
{
	email: {email},
	password: {password}
}

(success) 200
{
	token: {token}
}

(wrong password) 401


Revoke authorization?

User

Insert user
POST /api/user
{
	email: {email}
	password: {password}
	fname: {fname}
	lname: {lname}
}

(user already exists)  400
(success) 200, no response data


Delete user
DELETE /api/user
X-SessionId: {token}
{
	password: {password}
}	

(success) 200, no response data
(session id invalid) 401
(password wrong) 401

Update password

PUT /api/user
X-SessionId: {token}
{
	oldPassword: {old password},
	newPassword: {new password}
}

(success) 200
(invalid token) 401
(wrong password) 401

Listings

Fetch all listings
GET /api/listings?query={query}&category={category}&start={start}&stop={stop}
X-SessionId: {token}

[
	{
		id: {listing_id}
		name: {listing 1 name},
		owner: {owner email},
		askingPrice: {askingPrice},
category: {category},	
		description: {listing 2 description},
		imageUrls: [ image url 1, 2, 3],
		replies: {number of replies}
}, {
		id: {listing_id},
		name: {listing 2 name},
		owner: {owner email},
		askingPrice: {askingPrice},
		category: {category},
		description: {listing 3 description},
		imageUrls: [ image url 1, 2, 3],
		replies: {number of replies}
}, etc
]

query: optional search term for title
category: optional category
token: authorization token
start: inclusive optional start listing (not listing id, but an index into the results)
stop: exclusive optional stop listing (not listing id, but an index into the results) 


Fetch a specific listing
GET/api/listings/{listing_id}
X-SessionId: {token}

{
		id: {listing_id},
		name: {listing_id name},
		owner: {owner email},
		askingPrice: {askingPrice},
		description: {listing_id description},
category: {category},
		imageUrls: [ image url 1, 2, 3]
		replies: {number of replies}
}
(wrong listing id) 400
(wrong session id) 401

Add a listing
POST /api/listings
X-SessionId: {token}
{
title: {title},
askingPrice: {asking price},
category: {category},
description: {description},
images: []
}
(wrong session id) 401
(missing params) 400

Update a listing
PUT /api/listings/{listing_id}
X-SessionId: {token}
{
title: {new title}*,
category: {new category}*,
askingPrice: {new asking price}*,
description: {new description}*,
images: [new images]*
}

(invalid sessio id) 401
(wrong listing id) 400

any omitted fields remain the same?

Delete a listing
DELETE /api/listings/{listing_id}
X-SessionId: {token}

(wrong session id) 401
(wrong listing id) 400

returns 200 on success, etc

Add a Reply
POST /api/replies/{listing_id}
X-SessionId: {token}

{
	text: {text}
	dateTime: {datetime}
}

(success) 200
(wrong listing id) 400


Get all Replies
GET /api/replies/{listing_id}
X-SessionId: {token}

[
	{
		text: {text}
		dateTime: {dateTime}
	},
	{
		text: {text}
		dateTime: {dateTime}
	}
]



