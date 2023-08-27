# 💰 Exchange app

Simple Symfony 6.3 app for exchanging money. 

Project is dockerized and should be easy to set up locally. Just need to clone itself to your local and just execute commands:

````
make start
make migrate
````

Then app should be available for you in your browser on the URL: http://localhost:9001/

There are also some extra prometheus metrics available on the URL: http://localhost:9001/metrics/prometheus

For more info please take a look on Makefile.