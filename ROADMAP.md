# Start Playing

Ability to start playing immediately. No Account creation.
Clicking the 'Start playing' button creates a `Character` and temporary `User` (for WS connection) and
transports the user to the Character screen.
From there the `User` can be turned into an account by providing email and password.

# Arena Names

Could be constructed from various parts. For example:

> Horrific Blood Ring of Albion

or

> Gruesome Fighters Pit of Dundee

So the typical arena name would look like this "`<prefix>` `<arena_type>` of `<region>`" where each of these variables could have an effect on the arena properties.

There could also be "named" arenas, which are always the same. They would have an unique recognizable name and naming schema and should provide a challenge for characters.

# Svelte Store and WebSocket Concept

Stores encapsulate the business logic of the API.

For example the character store provides the `matchMake` method to start matchmaking.
This method will call the appropriate REST endpoint end expects an updated object in return.

In cases where the server proactively updates the FE state (push), those updates are provided using the WebSocket connections.

Every store should subscribe itself for updates at the `wsStoreManager`. The manager will check the received payload if it contains updates for the
subscribed stores. If it does, the stores callback method is called so that it can update its values.