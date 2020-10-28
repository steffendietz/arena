import { SFSocket } from '@spiralscout/websockets';

const socketOptions = { host: 'localhost', port: 8080, path: 'ws' };

// create an instance of SFSocket
const ws = new SFSocket(socketOptions);

const prepareEvent = event => doSomething(event);

// subscribe to server
ws.subscribe('message', prepareEvent);

// runtime ready for all instances
SFSocket.ready();

// unsubscribe from server 
ws.unsubscribe('message', prepareEvent);

// disconnect from server 
//ws.disconnect();

var specialTestFunction = function(bla) {
    var blubb = bla || 'Hello World!';
    console.log(blubb);
};

specialTestFunction();

if (module.hot) {
    module.hot.accept()
}