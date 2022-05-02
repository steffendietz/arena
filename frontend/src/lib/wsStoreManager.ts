function createStoreManager() {
    const ws = new WebSocket('ws://localhost:8080/ws');

    const isLoggedIn: boolean = userUuid !== null;

    const subscribers = [];

    ws.onopen = e => {
        const messageJoinChannel = {
            command: 'join',
            topics: ['channel']
        };

        ws.send(JSON.stringify(messageJoinChannel));

        if (isLoggedIn) {
            const messageJoinUserChannel = {
                command: 'join',
                topics: ['channel.' + userUuid]
            };

            ws.send(JSON.stringify(messageJoinUserChannel));
        }
    };

    ws.onmessage = e => {
        const message = JSON.parse(e.data);

        let payloadData = {};
        try {
            payloadData = JSON.parse(message.payload);           
        } catch (error) {
            payloadData = {};
        }

        console.log(`${message.topic}: ${message.payload}`, payloadData);

        subscribers.forEach(subscriber => {
            if(payloadData.hasOwnProperty(subscriber.namespace)) {
                subscriber.callback(payloadData[subscriber.namespace]);
            }
        });
    }

    return {
        subscribeForStoreUpdate(namespace: string, callback: Function) {
            subscribers.push({
                namespace,
                callback
            });
        }
    };
}

export const wsStoreManager = createStoreManager();
