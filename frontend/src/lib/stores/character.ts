import { request } from "../typed-client/request";
import type { Character } from "../types/character";
import { writable } from "svelte/store";
import { wsStoreManager } from "../wsStoreManager";

const getCharacters = async (): Promise<Character[]> => {
    return await request<Character[]>('/v1/character', { method: 'get' })
        .then(res => {
            return res.data;
        });
};

const matchMake = async (uuid: string): Promise<Character> => {
    return await request<Character>('/v1/character/toggleMatchSearch/' + uuid, { method: 'post' })
        .then(res => {
            return res.data;
        });
}

function createCharacterStore() {
    const { subscribe, set, update } = writable<Character[]>([]);

    getCharacters().then(set);
    wsStoreManager.subscribeForStoreUpdate('test', payload => {
        console.log('Hello from character store', payload);

    });

    return {
        subscribe,
        matchMake(uuid: string): void {
            console.log('matchmaking', uuid);
            matchMake(uuid).then(res => {
                update(chars => chars.map(char => {
                    return char.id === res.id
                        ? res
                        : char;
                }));
                console.log('request finished', res);
            });
        }
    };
}

export const characters = createCharacterStore();