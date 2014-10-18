#GT Thrift Shop - Web Client

### Setup

#### Dependencies

* [Node.js](http://nodejs.org)
* [npm](https://www.npmjs.org)

#### Installation

```sh
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
