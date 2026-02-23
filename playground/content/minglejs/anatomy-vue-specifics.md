# VueJS Anatomy

The Mingle command will generate 2 files: one for the Mingle registration and another for the JS component.

- `resources/js/ChatApp/index.js`

  Here we just register the Mingle and pass the JS component to it.
  ```js
  import mingle from '@mingle/mingleVue'
  import ChatApp from './ChatApp.vue'
    
  mingle('resources/js/ChatApp/index.js', ChatApp)
  ```        


- `resources/js/ChatApp/ChatApp.vue`

  Here we can define the Vue component. It's a regular component, we can now import sub-components, use Vue directives,
  etc.

  On initialization, we can retrieve `mingleData`.
  Also, we use the `wire` to interact with the Livewire component.
  ```vue
  <script setup>
  const props = defineProps({
      wire: {},
      mingleData: {},
  })
  
  const {wire, mingleData} = props
  
  const message = mingleData.message
  
  console.log(message) // 'Message in a bottle 🍾'
  
  wire.doubleIt(2)
      .then(data => {
          console.log(data) // 4
      })
  
  </script>
  
  <template>
      <div>
          <!-- Build something great! -->
      </div>
  </template>
  ```

## Advanced VueJS Components

Should you need to have full control over the Vue component, you can use the `createMingle()` function.

- The `createApp` function is the original Vue with runtime compiler.
- The `props` object is created for you for conveninence, but of course, you can modify it.
- You can even make some logic depending on initial `mingleData` that comes from the server.

When you are done, return `true` to indicate that the component is ready to be used.
```js
import mingle, { createMingle } from '@mingle/mingleVue'
import Counter from './Counter.vue'

const helloPlugin = {
    install: (app, options) => {
        app.config.globalProperties.$hello = (name) => {
            alert(`Hello ${name}!`)
        }
    }
}

createMingle('resources/js/vue-counter/index.js', ({createApp, props, el, wire, mingleId, wireId, mingleData}) => {
    const app = createApp(Counter, props)
    app.use(helloPlugin)
    app.mount(el)
    return true
})

```
