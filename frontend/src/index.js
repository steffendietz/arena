import { NamesDict, SFSocket } from '@spiralscout/websockets';

const socketOptions = { host: 'localhost', port: 8080, path: 'ws' };

// create an instance of SFSocket
const ws = new SFSocket(socketOptions);

SFSocket.ready();

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

if (module.hot) {
    module.hot.accept()
}