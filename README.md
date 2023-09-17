# Animal Crossing is Fun

https://animalcrossing.mave.me/

---

Animal Crossing is Fun is a website where you can keep track of your caught bugs, your caught fish, your found fossils, and your collected recipes. 

## Installation

Requirements:
* PHP >= 8.2
* Composer
* An available Redis instance (see below)
* A webserver (Apache / Nginx)


### Set up docker container for Redis

```
docker pull redis
# Create a folder for persistent data
mdkir /var/data/ac-is-fun
# Run a docker container, mounting the volume, and expose only 6379 to its own host
docker run -d --name redis-aniamlcrossing-is-fun -p 127.0.0.1:6379:6379 -v /var/data/ac-is-fun:/data redis
```

### Deploy website

```
git clone https://github.com/Ma-ve/AnimalCrossingIsFun.git
composer install
# Done
```

## Updating source data

I primarily use https://github.com/sungyeonu/animal-crossing-scraper/ for updating the `./data/*.json` files. Then, as defined in `composer.json`, the following commands are available:

```
# Convert JSON files to PHP files which is then included in the \Mave\AnimalCrossingIsFun\Repositories\Services\PhpService
composer run-script json-to-php

# Loop through each PHP file, and create a URL / indexing safe name for each item
composer run-script safename-item
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## Contact

Feel free to contact me through a GitHub issue, Pull Request, or by email via `animalcrossing [at] mave [dot] me`
