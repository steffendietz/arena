const ws = new WebSocket('ws://localhost:8181/ws');

ws.onopen = e => {
    const messageJoinChannel = {
        command: 'join',
        topics: ['channel']
    };

    ws.send(JSON.stringify(messageJoinChannel));

    if (userUuid !== null) {
        const messageJoinUserChannel = {
            command: 'join',
            topics: ['channel.' + userUuid]
        };

        ws.send(JSON.stringify(messageJoinUserChannel));
    }
};

ws.onmessage = e => {
    const message = JSON.parse(e.data);

    console.log(`${message.topic}: ${message.payload}`);
}

if (module.hot) {
    module.hot.accept()
}