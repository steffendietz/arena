<script lang="ts">
    import Character from "./Character.svelte";
    import { characters } from "../lib/stores/character";
</script>

<ul  class="characterList">
    {#each $characters as character}
        <Character {character} />
    {/each}
  </ul>
