<script lang="ts">
  import CharacterList from './components/CharacterList.svelte'

  const isLoggedIn: boolean = userUuid !== null;
</script>

<main>
  {#if isLoggedIn}
    <CharacterList />   
  {/if}
</main>

<style>
  :root {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
      Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  }

  main {
    text-align: center;
    padding: 1em;
    margin: 0 auto;
  }
</style>
