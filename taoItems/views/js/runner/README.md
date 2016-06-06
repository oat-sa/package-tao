# Runner quick overview

> The API is in draft

There should'nt be major changes expect for the following points:
 - `init` may also received the state (but it seems setState to be sufficient)
 - the `ready` event may be renamed to `render` or aliased.
 - some other events can appears

## Concept

The `ItemRunner` is the public API. A TestRunner calls the `ItemRunner` with itemData to render it and manage it's lifecycle.

It works in 2 steps:

1. Register a provider for the item type it will render. (This step is done only once, until the page is reloaded)

```
+----------------+             +--------------------+
|                |   register  |                    |
|   ItemRunner   <------+------+ QtiRuntimeProvider |
|                |1,n   |      |                    |
+----------------+      |      +--------------------+
                        |
                        |      +--------------------+
                        |      |                    |
                        +------+ OWIRuntimeProvider |
                        |      |                    |
                        |      +--------------------+
                        |
                        |      +--------------------+
                        |      |                    |
                        +------+ SomeOtherProvider  |
                               |                    |
                               +--------------------+
```

2. Create an `ItemRunner` instance for each item to render.

```
+--------------------------------------------------+               +----------------------------------------------+
|    ItemRunner                                    |               |    Provider                                  |
|--------------------------------------------------|               |----------------------------------------------|
|    <construct>(Object itemData) : ItemRunner     |               |                                              |
|                                                  |  delegates    |                                              |
|    init() : ItemRunner                        +------------------->  init(Object data, Func done) : void        |
|    render(HTMLElement elt) : ItemRunner       +------------------->  render(HTMLElement elt, Func done) : void  |
|    getState() : Object                        +------------------->  getState() : Object                        |
|    setState(Object state) : ItemRunner        +------------------->  setState(Object state) : void              |
|    getResponses() : Array                     +------------------->  getResponses() : Array                     |
|    clear() : ItemRunner                       +------------------->  clear() : void                             |
|                                                  |               |                                              |
|    on(event,Func handler) : ItemRunner           |               |                                              |
|    off(event) : ItemRunner                       |               |                                              |
|    trigger(event) : ItemRunner                   |               |                                              |
+--------------------------------------------------+               +----------------------------------------------+
```

## Sample

### Register a provider

```javascript
define(['itemRunner', 'qtiRuntimeProvider'], function(itemRunner, qtiRuntimeProvider){
    itemRunner.register('qti', qtiRuntimeProvider);
});
```


### Manipulate the itemRunner

Once the provider has been registered.

```javascript
define(['itemRunner'], function(itemRunner){

    var itemData = {
        //an object that represents the item
    };

    var initialState = {
        //an object with item current state
    };

                                        //itemRunner is a factory that creates a chainable instance.
    itemRunner('qti', itemData)         //qti is the name of the provider registered previously

		.on('error', function(err){
			//gracefull error handling
        })

        .on('init', function(){         //if the initialization is asynchronous it's better to render once init is done
            this.render(document.getElementById('item-container'));
        })

        .on('ready', function(){       //ready when render is finished. The test taker can start working, you can hide the loader, start a timer, etc.
            var self = this;           //here this is the item runner, so you have access to getState, getResponses, etc.

            //you can implement here the previous/next features, for example
            document.getElementById('next').addEventListener('click', function(){
                self.getResponses();    //store the responses
                self.getState();        //store the state

				self.clear(); 			//destroy the item propertly

                //forward to next item.
            });
        })

        .on('statechange', function(state){
            //oh something has changed in the item, you can store the state.
        })

        .setState(initialState)

        .init();    //let's start
});
```

## API

> Formal API is going to be generated using JsDoc.

Implemented events are :

 - `error` : any time, when somthing goes wrong
 - `init`  : once the initialization step is finished
 - `ready` : once the item is rendered and ready to be taken
 - `clear` : once the item is destroyed
 - `statechange` : each time the state has changed ( except using the public `setState`  - _to be confirmed_ )
 - `responsechange` : each time a response changes. It doesn't give you the last response but all responses entered by a test taker. Usefull to track user actions (_to be confirmed_ )
 - `endattempt` : the item informs the end of the attempt

## Test

Run in your browser, from a valid TAO distribution following the test case: `http://{TAO_HOST}/taoItems/views/js/test/runner/api/test.html?coverage=true`

## Build

_TBD_
