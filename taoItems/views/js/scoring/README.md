# Scoring quick overview

> The API is in draft and is subject to changes


## Concept
 
The `Scorer` is the public API. 

It works in 2 steps:

1. Register a provider (The provider provides a way to grade responses regarding processing data). (This step is done only once, until the page is reloaded)

```
+----------------+             +--------------------+
|                |   register  |                    |
|   Scorer       <------+------+ QtiScoringProvider |
|                |1,n   |      |                    |
+----------------+      |      +--------------------+
                        |                            
                        |      +--------------------+
                        |      |                    |
                        +------+ SomeOtherProvider  |
                               |                    |
                               +--------------------+
```
   
2. Create an `Scorer` instance for each scoring

```
+-------------------------------------------------------------+               +----------------------------------------------+
|    Scorer                                                   |               |    Provider                                  |
|-------------------------------------------------------------|               |----------------------------------------------|
|    <construct>(Object options) : Scorer                     |               |                                              |
|                                                             |  delegates    |                                              |
|    process(Array responses, Object Processing) : Scorer  +------------------->  render(HTMLElement elt, Func done) : void  |
|                                                             |               |                                              |
|    on(event,Func handler) : Scorer                          |               |                                              |
|    off(event) : Scorer                                      |               |                                              |
|    trigger(event) : Scorer                                  |               |                                              |
+-------------------------------------------------------------+               +----------------------------------------------+
```

## Sample

### Register a provider

```javascript
define(['scorer', 'qtiScoringProvider'], function(scorer, qtiScoringProvider){
    scorer.register('qti', qtiScoringProvider);
});
```


### Process responsese

Once the provider has been registered.

```javascript
define(['scorer'], function(scorer){

    //an array of responses
    var responses = [{
        RESPONSE : { base : { identifier : 'Attantis' } }
    }];    

    //processing rules  
    var processing = {
        //TBD
    };

                                        //scorer is a factory that creates a chainable instance.
    scorer('qti')                       //qti is the name of the provider registered previously

		.on('error', function(err){         
			//gracefull error handling
        })

        .on('outcome', function(outcome){       //we've got some outcome
            //outcome contains the SCORE and other outcome variables
        })

        .process(responses, processing);        //let's start scoring
});
```

