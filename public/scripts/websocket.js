const ws = new WebSocket('ws://localhost:8181/ws');

ws.onopen = e => {
    const message = {
        command: 'join',
        topics:  ['channel']
    };

    ws.send(JSON.stringify(message));
};

ws.onmessage = e => {
    const message = JSON.parse(e.data);

    console.log(`${message.topic}: ${message.payload}`);
}