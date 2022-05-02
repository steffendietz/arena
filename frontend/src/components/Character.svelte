<script lang="ts">
    import type { Character } from "../lib/types/character";
    import { characters } from "../lib/stores/character";

    export let character: Character;

    function matchMake() {
        characters.matchMake(character.id);
    }
</script>

<li>
    <span>{character.id}</span> {character.name}
    <button on:click={matchMake} disabled={character.isFighting}>
        {#if character.isSearching}matchmaking ...{:else}search{/if}
    </button>
</li>

<style>
    span {
        font-family: monospace;
    }
</style>
