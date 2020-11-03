import { NamesDict, SFSocket } from '@spiralscout/websockets';

const socketOptions = { host: 'localhost', port: 8080, path: 'ws' };

// create an instance of SFSocket
const ws = new SFSocket(socketOptions);

SFSocket.ready();

const channel = ws.joinChannel('channel');
channel.subscribe(NamesDict.MESSAGE, (e) => {
    console.log(e.data);
    if (e.data === 'leave') {
        channel.leave();
        console.log('channel left');
    }
});
if(userUuid !== null) {
    const userChannel = ws.joinChannel('channel.' + userUuid);
    userChannel.subscribe(NamesDict.MESSAGE, (e) => {
        console.log(e.data);
        if (e.data === 'leave') {
            userChannel.leave();
            console.log('userChannel left');
        }
    });
}

var specialTestFunction = function (bla) {
    var blubb = bla || 'Hello World!';
    console.log(blubb);
};

specialTestFunction();

if (module.hot) {
    module.hot.accept()
}