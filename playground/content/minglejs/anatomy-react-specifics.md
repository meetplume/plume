# ReactJS Anatomy

The command will generate 2 files: one for the Mingle registration and another for the JS component.

- `resources/js/ChatApp/index.js`
 
    Here we just register the Mingle and pass the JS component to it. 
    ```js
    import mingle from '@mingle/mingleReact'
    import ChatApp from './ChatApp.jsx'
    
    mingle('resources/js/ChatApp/index.js', ChatApp)
    
    ```        


- `resources/js/ChatApp/ChatApp.jsx`

    Here we can define the React component. It's a regular component, we can now import sub-components, use Vue directives, etc.
    
    On initialization, we can retrieve `mingleData`. 
    Also, we use the `wire` to interact with the Livewire component.
    ```jsx
    import React from 'react'
    
    function ChatApp({wire, ...props}) {
    
        const message = props.mingleData.message
    
        console.log(message) // 'Message in a bottle 🍾'
    
        wire.doubleIt(2)
            .then(data => {
                console.log(data) // 4
            })
    
        return (
            <div>
                {/* <!-- Create something great! --> */}
            </div>
        )
    }
    
    export default ChatApp
    ```

